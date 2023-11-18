<?php

namespace Database\Repositories;

use App\Models\UserFollowing;

class FollowingRepo
{
    public static function store(int $userId, int $followableId)
    {
        return UserFollowing::create([
            'user_id' => $userId,
            'following_id' => $followableId,
        ]);
    }
}
