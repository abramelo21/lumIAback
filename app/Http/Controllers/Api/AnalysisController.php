<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Analysis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function show(Request $request, Analysis $analysis): JsonResponse
    {
        $this->authorize('view', $analysis);
        $analysis->load(['resume', 'report', 'feedback']);

        return response()->json([
            'data' => [
                'id' => $analysis->id,
                'status' => $analysis->status->value,
                'resume_id' => $analysis->resume_id,
                'provider' => $analysis->provider,
                'model' => $analysis->model,
                'prompt_version' => $analysis->prompt_version,
                'error_message' => $analysis->error_message,
                'started_at' => $analysis->started_at,
                'completed_at' => $analysis->completed_at,
                'feedback' => $analysis->feedback,
                'report' => $analysis->report,
            ],
        ]);
    }

    public function report(Request $request, Analysis $analysis): JsonResponse
    {
        $this->authorize('view', $analysis);
        abort_unless($analysis->report, 404, 'Relatório ainda não disponível.');

        return response()->json(['data' => $analysis->report]);
    }

    public function feedback(StoreFeedbackRequest $request, Analysis $analysis): JsonResponse
    {
        $this->authorize('evaluate', $analysis);
        abort_unless($analysis->report, 422, 'Só é possível avaliar análises concluídas.');

        $feedback = $analysis->feedback()->updateOrCreate(
            ['analysis_id' => $analysis->id],
            $request->validated()
        );

        return response()->json(['data' => $feedback], 201);
    }
}
