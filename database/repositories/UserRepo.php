<?php

namespace Database\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserRepo
{
    public function getUser($id)
    {
        return User::query()->findOrFail($id);
    }

    public static function getUserByToken(Request $request)
    {
        // Get token from request
        $token = $request->header('Authorization');

        // Trim off "Bearer " prefix
        $token = str_replace('Bearer ', '', $token);

        // Find user by Access token
        $user = User::where('access_token', $token)->first();

        // Check if user was found
        if (!$user) {
            return false;
        }

        // Return user object
        return $user;
    }

    protected static function followableUsers(int $userId)
    {
        return User::query()
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereColumn('user_id', 'users.id')
                    ->where('completed', 0);
            })
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('following_id')
                    ->from('user_followings')
                    ->where('user_id', $userId);
            })
            ->where('id', '!=', $userId);
    }

    public static function getAllFollowableUsers(int $userId)
    {
        return self::followableUsers($userId)->get();
    }

    public static function paginateFollowableUsers(int $userId, $perPage = 10)
    {
        return self::followableUsers($userId)->paginate($perPage);
    }

    public static function validateFollowable(User $user, int $followableId)
    {
        // Get followable users for auth user
        $followableUsers = self::getAllFollowableUsers($user->id);

        // Check if followable ID is in list
        $isValid = $followableUsers->contains(function ($user) use ($followableId) {
            return $user->id == $followableId;
        });

        return $isValid;
    }

    public static function decreaseCoin(User $user, int $number)
    {
        $user->coin_balance -= $number;
        $user->save();
        return $user;
    }
    public static function increaseCoin(User $user, int $number)
    {
        $user->coin_balance += $number;
        $user->save();
        return $user;
    }
}
