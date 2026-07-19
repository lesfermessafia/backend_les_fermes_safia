<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\StockActivityNotification;
use Illuminate\Support\Facades\Auth;

class StockNotificationService
{
    public static function notifyRoles(
        string $title,
        string $message,
        string $category,
        ?string $url = null,
        string $color = 'blue',
        ?int $exceptUserId = null,
    ): void {
        if ($exceptUserId === null) {
            $exceptUserId = Auth::id() ? (int) Auth::id() : null;
        }

        self::recipients()
            ->when($exceptUserId !== null, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->get()
            ->each(fn (User $user) => $user->notify(
                new StockActivityNotification($title, $message, $category, $url, $color)
            ));
    }

    public static function notifyRolesExcept(
        int $userId,
        string $title,
        string $message,
        string $category,
        ?string $url = null,
        string $color = 'blue'
    ): void {
        self::notifyRoles($title, $message, $category, $url, $color, $userId);
    }

    private static function recipients()
    {
        return User::whereIn('role', ['admin', 'comptable'])
            ->where('bloquer', false);
    }
}
