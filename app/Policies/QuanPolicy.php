<?php

namespace App\Policies;

use App\Models\Quan;
use App\Models\User;

class QuanPolicy
{
    public function before(?User $user, string $ability): ?bool
    {
        if ($user) {
            if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied('quan_ly_quan')) {
                return false;
            }

            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('quan_ly_quan')) {
                return true;
            }

            if ($user->isAdmin() || $user->hasRole('admin')) {
                return true;
            }
        }

        return null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Quan $quan): bool
    {
        if ($quan->trashed() || $quan->trang_thai !== 'da_duyet') {
            return $user !== null && (($user->hasPermissionTo('quan_ly_quan')) || $user->id === $quan->chu_quan_id);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quan $quan): bool
    {
        return ($user->hasPermissionTo('quan_ly_quan')) || $user->id === $quan->chu_quan_id;
    }

    public function delete(User $user, Quan $quan): bool
    {
        return ($user->hasPermissionTo('quan_ly_quan')) || $user->id === $quan->chu_quan_id;
    }

    public function restore(User $user, Quan $quan): bool
    {
        return $user->hasPermissionTo('quan_ly_quan');
    }

    public function forceDelete(User $user, Quan $quan): bool
    {
        return $user->hasPermissionTo('quan_ly_quan');
    }
}
