<?php

namespace App\Http\Requests;

use App\Enums\ProfessionalArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = (int) config('lumia.max_upload_kilobytes', 5120);

        return [
            'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:'.$maxKb],
            'target_area' => ['required', Rule::enum(ProfessionalArea::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Envie apenas currículos em PDF com texto selecionável.',
            'file.max' => 'O arquivo deve ter no máximo '.round($this->maxKilobytes() / 1024, 1).' MB.',
        ];
    }

    private function maxKilobytes(): int
    {
        return (int) config('lumia.max_upload_kilobytes', 5120);
    }
}
