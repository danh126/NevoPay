<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $sender = Wallet::factory()->create();
        $receiver = Wallet::factory()->create();

        return [
            'wallet_id' => $sender->id,
            'sender_wallet_id' => $sender->id,
            'receiver_wallet_id' => $receiver->id,
            'type' => 'transfer',
            'amount' => fake()->randomFloat(2, 10000, 50000000),
            'status' => 'pending',
            'description' => fake()->sentence(),
            'created_by' => $sender->user_id,
            'completed_at' => null,
        ];
    }
}
