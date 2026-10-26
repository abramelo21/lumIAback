<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'analysis_id',
    'summary',
    'identified_skills',
    'strengths',
    'improvement_points',
    'project_recommendations',
    'experience_recommendations',
    'action_plan',
    'structure_notes',
    'area_fit',
])]
class AnalysisReport extends Model
{
    protected function casts(): array
    {
        return [
            'identified_skills' => 'array',
            'strengths' => 'array',
            'improvement_points' => 'array',
            'project_recommendations' => 'array',
            'experience_recommendations' => 'array',
            'action_plan' => 'array',
            'structure_notes' => 'array',
            'area_fit' => 'array',
        ];
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }
}
