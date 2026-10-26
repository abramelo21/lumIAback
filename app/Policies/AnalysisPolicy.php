<?php

namespace App\Policies;

use App\Models\Analysis;
use App\Models\User;

class AnalysisPolicy
{
    public function view(User $user, Analysis $analysis): bool
    {
        return $user->isAdmin() || $user->id === $analysis->resume->user_id;
    }

    public function evaluate(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->resume->user_id;
    }
}
