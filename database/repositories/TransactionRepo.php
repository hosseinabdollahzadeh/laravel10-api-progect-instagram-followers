<?php

namespace Database\Repositories;

use App\Models\Transaction;
use App\Models\User;

class TransactionRepo
{
    public static function store(User $user, int $orderId, int $coinAmount)
    {
        Transaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'coin_amount' => $coinAmount
        ]);
    }
}
