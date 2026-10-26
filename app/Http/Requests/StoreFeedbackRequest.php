<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'usefulness_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'clarity_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'improvement_perception_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
