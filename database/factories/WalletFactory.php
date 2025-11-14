<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'wallet_number' => fake()->unique()->numerify('############'), // 12 số
            'balance' => fake()->randomFloat(2, 0, 10000),
            'currency' => 'VNĐ',
            'is_active' => true,
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
