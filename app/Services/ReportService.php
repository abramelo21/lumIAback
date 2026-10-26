<?php

namespace App\Services;

use App\Enums\AnalysisStatus;
use App\Enums\ImprovementCategory;
use App\Enums\ResumeStatus;
use App\Models\Analysis;
use App\Models\Feedback;
use App\Models\Resume;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $received = Resume::query()->count();
        $completed = Resume::query()->where('status', ResumeStatus::Completed)->count();
        $failed = Analysis::query()->where('status', AnalysisStatus::Failed)->count();
        $started = Analysis::query()->whereNotNull('started_at')->count();

        $avgSeconds = Analysis::query()
            ->where('status', AnalysisStatus::Completed)
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) as avg_seconds')
            ->value('avg_seconds');

        $areas = Resume::query()
            ->select('target_area', DB::raw('count(*) as total'))
            ->groupBy('target_area')
            ->get()
            ->map(fn ($row) => [
                'area' => $row->target_area?->value,
                'label' => $row->target_area?->label(),
                'total' => (int) $row->total,
            ]);

        $categories = [];
        foreach (ImprovementCategory::cases() as $category) {
            $categories[$category->value] = [
                'category' => $category->value,
                'label' => $category->label(),
                'total' => 0,
            ];
        }

        foreach (Analysis::query()->where('status', AnalysisStatus::Completed)->with('report')->get() as $analysis) {
            foreach ($analysis->report?->improvement_points ?? [] as $point) {
                $key = $point['category'] ?? null;
                if ($key && isset($categories[$key])) {
                    $categories[$key]['total']++;
                }
            }
        }

        return [
            'resumes_received' => $received,
            'resumes_processed' => $completed,
            'analyses_completed' => Analysis::query()->where('status', AnalysisStatus::Completed)->count(),
            'analyses_failed' => $failed,
            'completion_rate' => $started > 0 ? round(Analysis::query()->where('status', AnalysisStatus::Completed)->count() / $started, 3) : 0,
            'failure_rate' => $started > 0 ? round($failed / $started, 3) : 0,
            'average_processing_seconds' => $avgSeconds ? (int) round($avgSeconds) : null,
            'average_satisfaction' => round((float) Feedback::query()->avg('rating'), 2),
            'average_usefulness' => round((float) Feedback::query()->avg('usefulness_rating'), 2),
            'average_clarity' => round((float) Feedback::query()->avg('clarity_rating'), 2),
            'areas' => $areas,
            'improvement_categories' => array_values($categories),
        ];
    }
}
