<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = Wallet::all();

        // Cache trước để tránh query lặp
        $adminId = User::where('role', 'admin')->first()->id;
        $userIds = User::where('role', 'user')->pluck('id')->toArray();

        for ($i = 0; $i < 40; $i++) {

            $type = fake()->randomElement(['deposit', 'withdraw', 'transfer']);

            $wallet = $wallets->random();
            $amount = rand(10000, 2000000);

            // Xác định ai tạo
            $creatorType = fake()->randomElement(['system', 'admin', 'user']);

            $createdBy = match ($creatorType) {
                'admin' => $adminId,
                'user'  => fake()->randomElement($userIds),
                default => null, // system
            };

            $data = [
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'completed',
                'description' => ucfirst($type) . ' sample',
                'created_by' => $createdBy,
                'completed_at' => now(),
            ];

            // Nếu là chuyển tiền → gắn sender/receiver
            if ($type === 'transfer') {
                $receiver = $wallets->where('id', '!=', $wallet->id)->random();

                $data['sender_wallet_id']   = $wallet->id;
                $data['receiver_wallet_id'] = $receiver->id;
            }

            Transaction::create($data);
        }
    }
}
