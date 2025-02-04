<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ProductCategory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('view ProductCategory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ProductCategory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('update ProductCategory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('delete ProductCategory');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ProductCategory');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('restore ProductCategory');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ProductCategory');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('replicate ProductCategory');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ProductCategory');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductCategory $productcategory): bool
    {
        return $user->checkPermissionTo('force-delete ProductCategory');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ProductCategory');
    }
}
