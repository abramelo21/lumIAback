<?php

namespace App\Services\AI;

use App\Enums\AnalysisStatus;
use App\Enums\ResumeStatus;
use App\Models\Analysis;
use App\Models\AnalysisReport;
use App\Services\PersonalDataMinimizer;
use App\Services\ResumeProcessingService;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    public function __construct(
        private readonly AIProviderInterface $provider,
        private readonly ResumeProcessingService $processing,
        private readonly PersonalDataMinimizer $minimizer,
        private readonly AnalysisResultValidator $validator,
    ) {}

    public function process(Analysis $analysis): void
    {
        $resume = $analysis->resume;
        $analysis->update([
            'status' => AnalysisStatus::Processing,
            'provider' => $this->provider->name(),
            'model' => $this->provider->model(),
            'attempts' => $analysis->attempts + 1,
            'started_at' => $analysis->started_at ?? now(),
            'error_message' => null,
        ]);
        $resume->update(['status' => ResumeStatus::Processing]);

        try {
            $extracted = $this->processing->extractText($resume);
            $resume->update(['extracted_text' => $extracted]);

            $minimized = $this->minimizer->minimize($extracted);
            $raw = $this->provider->analyze(
                $minimized,
                $resume->target_area->label(),
                (string) $analysis->prompt_version
            );
            $validated = $this->validator->validate($raw);

            AnalysisReport::query()->updateOrCreate(
                ['analysis_id' => $analysis->id],
                $validated
            );

            $resume->update([
                'status' => ResumeStatus::Completed,
                'extracted_text' => null,
            ]);

            $analysis->update([
                'status' => AnalysisStatus::Completed,
                'completed_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Falha ao processar análise de currículo', [
                'analysis_id' => $analysis->id,
                'resume_id' => $resume->id,
                'error' => $exception->getMessage(),
            ]);

            $resume->update(['status' => ResumeStatus::Failed, 'extracted_text' => null]);
            $analysis->update([
                'status' => AnalysisStatus::Failed,
                'completed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
