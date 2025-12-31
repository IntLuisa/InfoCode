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
        return true;
    }
}
