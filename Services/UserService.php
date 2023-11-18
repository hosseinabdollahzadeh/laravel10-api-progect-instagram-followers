<?php

namespace Services;

use App\Models\User;
use App\Traits\ApiResponser;
use Database\Repositories\FollowerRepo;
use Database\Repositories\FollowingRepo;
use Database\Repositories\OrderRepo;
use Database\Repositories\TransactionRepo;
use Database\Repositories\UserRepo;
use Illuminate\Support\Facades\DB;

class UserService
{
    use ApiResponser;

    public static function followerRequest(User $user, int $followerCount)
    {
        $required_coin = $followerCount * User::COINS_PAID_PER_FOLLOWER;

        // Create order
        if ($required_coin <= $user->coin_balance) {

            DB::beginTransaction();

            // Create order
            $order = OrderRepo::store($user, $followerCount);

            // Create transaction
            TransactionRepo::store($user, $order->id, $required_coin);

            UserRepo::decreaseCoin($user, User::COINS_PAID_PER_FOLLOWER);
            DB::commit();

        } else {
            return false;
        }

        return $order;
    }

    public static function followUser(User $follower, int $followableId)
    {
        DB::beginTransaction();

        // Update order
        $order = OrderRepo::getUncompletedOrder($followableId);
        if ($order) {
            $order->follower_count_remained--;
            if ($order->follower_count_remained == ($order->follower_count_requested - 1)) {
                $order->completed = true;
            }
            $order->save();
        } else {
            return false;
        }

        FollowingRepo::store($follower->id, $followableId);

        FollowerRepo::store($followableId, $follower->id);


        // Update follower balances
        UserRepo::increaseCoin($follower, User::BONUS_COINS_PER_FOLLOWING);

        DB::commit();

        return UserRepo::getUser($followableId);
    }
}
