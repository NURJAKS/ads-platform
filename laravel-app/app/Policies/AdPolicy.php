<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ad;

class AdPolicy
{
    /**
     * User can update his own ads or Admin can update any.
     */
    public function update(User $user, Ad $ad)
    {
        return $user->id === $ad->user_id || $user->role === 'admin';
    }

    /**
     * User can delete his own ads or Admin can delete any.
     */
    public function delete(User $user, Ad $ad)
    {
        return $user->id === $ad->user_id || $user->role === 'admin';
    }

    /**
     * Moderation — only admin or moderator.
     */
    public function moderate(User $user)
    {
        return in_array($user->role, ['admin', 'moderator']);
    }
}
