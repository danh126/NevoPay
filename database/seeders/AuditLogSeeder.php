<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Logs cho wallets
        Wallet::all()->each(function ($wallet) {
            AuditLog::create([
                'user_id' => $wallet->user_id,
                'action' => 'create',
                'auditable_type' => Wallet::class,
                'auditable_id' => $wallet->id,
                'old_values' => null,
                'new_values' => json_encode($wallet->toArray()),
                'description' => 'Wallet created via seeder',
            ]);
        });

        // Logs cho transactions
        Transaction::all()->each(function ($transaction) {
            AuditLog::create([
                'user_id' => null, // system tạo
                'action' => 'create',
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'old_values' => null,
                'new_values' => json_encode($transaction->toArray()),
                'description' => 'Transaction created via seeder',
            ]);
        });
    }
}
