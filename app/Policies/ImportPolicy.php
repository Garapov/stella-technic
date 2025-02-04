<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Import;
use App\Models\User;

class ImportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Import');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('view Import');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Import');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('update Import');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('delete Import');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Import');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('restore Import');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Import');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('replicate Import');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Import');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Import $import): bool
    {
        return $user->checkPermissionTo('force-delete Import');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Import');
    }
}
