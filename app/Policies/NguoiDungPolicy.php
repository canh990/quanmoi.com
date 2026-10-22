<?php

namespace App\Policies;

use App\Models\User;

class NguoiDungPolicy
{
    public function before(?User $user, string $ability): ?bool
    {
        if ($user) {
            if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied('quan_ly_nguoi_dung')) {
                return false;
            }

            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('quan_ly_nguoi_dung')) {
                return true;
            }

            if ($user->isAdmin() || $user->hasRole('admin')) {
                return true;
            }
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('quan_ly_nguoi_dung');
    }
}
