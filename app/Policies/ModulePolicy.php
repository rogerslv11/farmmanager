<?php

namespace App\Policies;

use App\Models\User;

class ModulePolicy
{
    public function viewAny(User $user, string $module = 'dashboard'): bool
    {
        return $user->active && $user->canAccess($module);
    }
}
