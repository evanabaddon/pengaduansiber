<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subsibansidik;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubsibansidikPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_subsibansidik');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('view_subsibansidik');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_subsibansidik');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('update_subsibansidik');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('delete_subsibansidik');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_subsibansidik');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('force_delete_subsibansidik');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_subsibansidik');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('restore_subsibansidik');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_subsibansidik');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Subsibansidik $subsibansidik): bool
    {
        return $user->can('replicate_subsibansidik');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_subsibansidik');
    }
}
