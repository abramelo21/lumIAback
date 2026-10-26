<?php

namespace App\Jobs;

use App\Models\Analysis;
use App\Services\AI\AIAnalysisService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessResumeAnalysis implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public int $uniqueFor = 300;

    public function __construct(public int $analysisId) {}

    public function uniqueId(): string
    {
        return (string) $this->analysisId;
    }

    public function handle(AIAnalysisService $service): void
    {
        $analysis = Analysis::query()->with('resume')->findOrFail($this->analysisId);

        if ($analysis->report()->exists()) {
            return;
        }

        $service->process($analysis);
    }
}
