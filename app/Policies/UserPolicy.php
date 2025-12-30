<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public const ROLE_BOARD = 'Board';
    public const ROLE_MANAGER = 'Manager';
    public const ROLE_FINANCE = 'Finance';
    public const ROLE_FINANCE_ASSISTANT = 'Financial Assistant';
    public const ROLE_ASSEMBLY_MANAGER = 'Assembly Manager';
    public const ROLE_CONSULTANT = 'Consultant';
    public const ROLE_ASSEMBLER = 'Assembler';
    public const ROLE_OBSERVER = 'Observer';

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD, self::ROLE_MANAGER]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD, self::ROLE_MANAGER]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD, self::ROLE_MANAGER]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD, self::ROLE_MANAGER]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return in_array($user->role, [self::ROLE_BOARD]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return false;
    }
}
