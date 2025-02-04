<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ProductParam;
use App\Models\User;

class ProductParamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ProductParam');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('view ProductParam');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ProductParam');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('update ProductParam');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('delete ProductParam');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ProductParam');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('restore ProductParam');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ProductParam');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('replicate ProductParam');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ProductParam');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductParam $productparam): bool
    {
        return $user->checkPermissionTo('force-delete ProductParam');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ProductParam');
    }
}
