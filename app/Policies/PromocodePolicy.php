<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Promocode;
use App\Models\User;

class PromocodePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Promocode');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('view Promocode');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Promocode');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('update Promocode');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('delete Promocode');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Promocode');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('restore Promocode');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Promocode');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('replicate Promocode');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Promocode');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Promocode $promocode): bool
    {
        return $user->checkPermissionTo('force-delete Promocode');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Promocode');
    }
}
