<?php
namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua role bisa melihat daftar aktivitas
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        if ($user->role === 'admin') {
            return true; // Admin bisa melihat semua aktivitas
        }

        return $user->role === 'dlh' || $user->project_id === $activity->project_id; // DLH bisa melihat aktivitas yang sesuai project
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']); // Admin dan User bisa create aktivitas
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        if ($user->role === 'admin') {
            return true; // Admin bisa update semua aktivitas
        }

        // User dan DLH hanya bisa mengupdate aktivitas sesuai dengan project mereka
        return $user->role === 'user' && $user->project_id === $activity->project_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return $user->role === 'admin'; // Hanya admin yang bisa delete aktivitas
    }

    /**
     * Optional: Restore or Force Delete
     */
    public function restore(User $user, Activity $activity): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return $user->role === 'admin';
    }
}
