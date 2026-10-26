<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analysis;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->reports->dashboard()]);
    }

    public function analyses(): JsonResponse
    {
        $items = Analysis::query()
            ->with(['resume.user:id,name,email', 'report:id,analysis_id'])
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Analysis $analysis) => [
                'id' => $analysis->id,
                'resume_id' => $analysis->resume_id,
                'status' => $analysis->status->value,
                'target_area' => $analysis->resume?->target_area?->value,
                'target_area_label' => $analysis->resume?->target_area?->label(),
                'original_filename' => $analysis->resume?->original_filename,
                'participant_name' => $analysis->resume?->user?->name,
                'participant_email' => $analysis->resume?->user?->email,
                'has_report' => (bool) $analysis->report,
                'started_at' => $analysis->started_at,
                'completed_at' => $analysis->completed_at,
                'error_message' => $analysis->error_message,
            ]);

        return response()->json(['data' => $items]);
    }
}
