<?php

namespace App\Support;

use App\Models\User;

final class UserAccess
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PA_USER = 'pa_user';

    public static function isAdmin(?User $user): bool
    {
        return $user !== null && $user->role === self::ROLE_ADMIN;
    }

    public static function isPaUser(?User $user): bool
    {
        return $user !== null && $user->role === self::ROLE_PA_USER;
    }

    public static function assignedProtectedAreaId(?User $user): ?int
    {
        if (! self::isPaUser($user)) {
            return null;
        }

        return $user->protected_area_id ? (int) $user->protected_area_id : null;
    }
}
