<?php

namespace App\Policies;

use App\Models\Quan;
use App\Models\User;

class QuanPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Quan $quan): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quan $quan): bool
    {
        return $user->isAdmin() || $user->id === $quan->chu_quan_id;
    }

    public function delete(User $user, Quan $quan): bool
    {
        return $user->isAdmin() || $user->id === $quan->chu_quan_id;
    }

    public function restore(User $user, Quan $quan): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Quan $quan): bool
    {
        return $user->isAdmin();
    }
}
