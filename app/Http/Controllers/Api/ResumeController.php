<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeRequest;
use App\Models\Resume;
use App\Services\ResumeProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function __construct(private readonly ResumeProcessingService $processing) {}

    public function index(Request $request): JsonResponse
    {
        $resumes = $request->user()
            ->resumes()
            ->with(['analyses' => fn ($q) => $q->latest()->limit(1)])
            ->latest()
            ->get()
            ->map(fn (Resume $resume) => $this->summary($resume));

        return response()->json(['data' => $resumes]);
    }

    public function store(StoreResumeRequest $request): JsonResponse
    {
        $resume = $this->processing->storeAndQueue(
            $request->user(),
            $request->file('file'),
            $request->string('target_area')->toString()
        );

        return response()->json(['data' => $this->summary($resume)], 201);
    }

    public function show(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('view', $resume);
        $resume->load(['analyses.report', 'analyses.feedback']);

        return response()->json(['data' => $this->detail($resume)]);
    }

    public function destroy(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('delete', $resume);
        $this->processing->deleteResume($resume);

        return response()->json(['message' => 'Currículo e dados associados foram excluídos.']);
    }

    public function status(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('view', $resume);
        $analysis = $resume->latestAnalysis();

        $messages = [
            'pending' => 'Seu currículo está na fila de análise.',
            'processing' => 'Seu currículo está sendo analisado.',
            'completed' => 'Sua análise está disponível.',
            'failed' => 'Não foi possível concluir a análise.',
        ];

        return response()->json([
            'resume_id' => $resume->id,
            'status' => $resume->status->value,
            'message' => $messages[$resume->status->value] ?? 'Status atualizado.',
            'report_available' => (bool) $analysis?->report,
            'analysis_id' => $analysis?->id,
            'error_message' => $resume->status->value === 'failed' ? $analysis?->error_message : null,
        ]);
    }

    public function retry(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('delete', $resume);

        $analysis = $this->processing->queueAnalysis($resume);

        return response()->json([
            'data' => $this->summary($resume->fresh(['analyses'])),
            'analysis_id' => $analysis->id,
        ]);
    }

    private function summary(Resume $resume): array
    {
        $analysis = $resume->latestAnalysis();

        return [
            'id' => $resume->id,
            'original_filename' => $resume->original_filename,
            'target_area' => $resume->target_area->value,
            'target_area_label' => $resume->target_area->label(),
            'status' => $resume->status->value,
            'file_size' => $resume->file_size,
            'created_at' => $resume->created_at,
            'analysis_id' => $analysis?->id,
            'report_id' => $analysis?->report?->id,
        ];
    }

    private function detail(Resume $resume): array
    {
        return $this->summary($resume) + [
            'mime_type' => $resume->mime_type,
            'retained_until' => $resume->retained_until,
            'analyses' => $resume->analyses->map(fn ($analysis) => [
                'id' => $analysis->id,
                'status' => $analysis->status->value,
                'provider' => $analysis->provider,
                'model' => $analysis->model,
                'error_message' => $analysis->error_message,
                'started_at' => $analysis->started_at,
                'completed_at' => $analysis->completed_at,
                'has_report' => (bool) $analysis->report,
                'has_feedback' => (bool) $analysis->feedback,
            ]),
        ];
    }
}
