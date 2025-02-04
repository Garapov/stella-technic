<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Former;
use App\Models\User;

class FormerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Former');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('view Former');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Former');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('update Former');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('delete Former');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Former');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('restore Former');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Former');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('replicate Former');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Former');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Former $former): bool
    {
        return $user->checkPermissionTo('force-delete Former');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Former');
    }
}
