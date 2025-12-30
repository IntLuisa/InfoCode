<?php

namespace App\Policies;

use App\Models\User;

class ContractPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserPolicy::ROLE_BOARD, UserPolicy::ROLE_MANAGER]);
    }
}
