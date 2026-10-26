<?php

namespace App\Models;

use App\Enums\AnalysisStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'resume_id',
    'status',
    'provider',
    'model',
    'prompt_version',
    'error_message',
    'attempts',
    'started_at',
    'completed_at',
])]
class Analysis extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AnalysisStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(AnalysisReport::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }
}
