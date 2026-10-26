<?php

namespace App\Models;

use App\Enums\ProfessionalArea;
use App\Enums\ResumeStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'original_filename',
    'storage_path',
    'mime_type',
    'file_size',
    'target_area',
    'status',
    'extracted_text',
    'retained_until',
])]
class Resume extends Model
{
    protected function casts(): array
    {
        return [
            'target_area' => ProfessionalArea::class,
            'status' => ResumeStatus::class,
            'retained_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function latestAnalysis(): ?Analysis
    {
        return $this->analyses()->latest('id')->first();
    }
}
