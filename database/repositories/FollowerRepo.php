<?php

namespace Database\Repositories;

use App\Models\UserFollower;

class FollowerRepo
{
    public static function store(int $userId, int $followerId)
    {
        return UserFollower::create([
            'user_id' => $userId,
            'follower_id' => $followerId,
        ]);
    }
}
