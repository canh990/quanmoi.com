<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Blog $blog): bool
    {
        if ($blog->status === 'published') {
            return true;
        }

        if ($user && ($user->isAdmin() || $user->id === $blog->user_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a draft
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Blog $blog): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $blog->user_id && in_array($blog->status, ['draft', 'need_revision', 'rejected'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Blog $blog): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $blog->user_id && ! in_array($blog->status, ['published', 'scheduled'])) {
            return true;
        }

        return false;
    }

    public function restore(User $user, Blog $blog): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Blog $blog): bool
    {
        return $user->isAdmin();
    }
}
