<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => rand(50000, 5000000),
                'currency' => 'VND',
                'is_active' => true,
            ]);
        });
    }
}
