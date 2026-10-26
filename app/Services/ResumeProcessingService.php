<?php

namespace App\Services;

use App\Enums\AnalysisStatus;
use App\Enums\ResumeStatus;
use App\Jobs\ProcessResumeAnalysis;
use App\Models\Analysis;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class ResumeProcessingService
{
    public function __construct(private readonly Parser $parser) {}

    public function storeAndQueue(User $user, UploadedFile $file, string $targetArea): Resume
    {
        $disk = (string) config('lumia.filesystem_disk', 'local');
        $path = sprintf('resumes/%s/%s.pdf', $user->id, Str::uuid());

        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

        $resume = Resume::query()->create([
            'user_id' => $user->id,
            'original_filename' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/pdf',
            'file_size' => $file->getSize(),
            'target_area' => $targetArea,
            'status' => ResumeStatus::Pending,
            'retained_until' => now()->addDays((int) config('lumia.retention_days')),
        ]);

        $this->queueAnalysis($resume);

        return $resume->fresh(['analyses']);
    }

    public function queueAnalysis(Resume $resume): Analysis
    {
        $analysis = Analysis::query()->create([
            'resume_id' => $resume->id,
            'status' => AnalysisStatus::Queued,
            'prompt_version' => config('lumia.prompt_version'),
        ]);

        $resume->update(['status' => ResumeStatus::Pending]);

        ProcessResumeAnalysis::dispatch($analysis->id);

        return $analysis;
    }

    public function extractText(Resume $resume): string
    {
        $disk = (string) config('lumia.filesystem_disk', 'local');
        $contents = Storage::disk($disk)->get($resume->storage_path);

        if (! $contents) {
            throw new \RuntimeException('Não foi possível localizar o arquivo do currículo.');
        }

        $temporary = tempnam(sys_get_temp_dir(), 'lumia_pdf_');
        file_put_contents($temporary, $contents);

        try {
            $pdf = $this->parser->parseFile($temporary);
            $text = trim($pdf->getText());
        } finally {
            @unlink($temporary);
        }

        if (mb_strlen($text) < 80) {
            throw new \RuntimeException(
                'Não foi possível extrair texto selecionável deste PDF. Na primeira versão, o LumiA processa apenas PDFs com texto, não digitalizações.'
            );
        }

        return $text;
    }

    public function deleteResume(Resume $resume): void
    {
        $disk = (string) config('lumia.filesystem_disk', 'local');
        Storage::disk($disk)->delete($resume->storage_path);
        $resume->delete();
    }
}
