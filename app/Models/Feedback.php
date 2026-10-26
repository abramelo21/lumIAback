<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'analysis_id',
    'rating',
    'usefulness_rating',
    'clarity_rating',
    'improvement_perception_rating',
    'comments',
])]
class Feedback extends Model
{
    protected $table = 'feedbacks';

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }
}
