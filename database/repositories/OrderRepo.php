<?php

namespace Database\Repositories;

use App\Models\Order;
use App\Models\User;

class OrderRepo
{
    public static function store(User $user, int $followerCount)
    {
        return Order::create([
            'user_id' => $user->id,
            'follower_count_requested' => $followerCount,
            'follower_count_remained' => $followerCount,
        ]);
    }

    public static function getUncompletedOrder(int $followableId)
    {
        return Order::query()
            ->where('user_id', $followableId)
            ->where('completed', false)
            ->first();
    }
}
