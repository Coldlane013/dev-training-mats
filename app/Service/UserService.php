<?php

namespace App\Service;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    /**
     * Count all logged-in users (users with valid active tokens).
     *
     * @return int
     */
    public function countLoggedInUsers(): int
    {
        return PersonalAccessToken::whereNotNull('last_used_at')
            ->distinct('tokenable_id')
            ->count('tokenable_id');
    }

    /**
     * Get all logged-in users with their token information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoggedInUsers()
    {
        return User::whereHas('tokens', function ($query) {
            $query->whereNotNull('last_used_at');
        })->get();
    }

    /**
     * Check if a user is currently logged in (has valid active tokens).
     *
     * @param int $userId
     * @return bool
     */
    public function isUserLoggedIn(int $userId): bool
    {
        return PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->whereNotNull('last_used_at')
            ->exists();
    }

    /**
     * Get count of logged-in users with details.
     *
     * @return array
     */
    public function getLoggedInStats(): array
    {
        $totalUsers = User::count();
        $loggedInCount = $this->countLoggedInUsers();
        $offlineCount = $totalUsers - $loggedInCount;

        return [
            'total_users' => $totalUsers,
            'logged_in' => $loggedInCount,
            'offline' => $offlineCount,
            'online_percentage' => $totalUsers > 0 ? round(($loggedInCount / $totalUsers) * 100, 2) : 0,
        ];
    }
}
