<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_send_transaction_success_logs_correct_message()
    {
        Log::spy();

        $transaction = new Transaction([
            'transaction_code' => 'TXN-12345',
            'type'             => 'deposit',
            'amount'           => 1000,
            'wallet_id'        => 5,
        ]);

        $service = new NotificationService();
        $service->sendTransactionSuccess($transaction);

        Log::shouldHaveReceived('info')
            ->once()
            ->with(
                "Sent transaction success notification",
                [
                    'transaction_code' => 'TXN-12345',
                    'type'             => 'deposit',
                    'amount'           => 1000,
                    'wallet_id'        => 5,
                ]
            );
    }

    public function test_send_transaction_failed_logs_correct_message()
    {
        Log::spy();

        $transaction = new Transaction([
            'transaction_code' => 'TXN-67890',
            'type'             => 'transfer',
            'amount'           => 500,
            'wallet_id'        => 9,
        ]);

        $reason = "Insufficient funds";

        $service = new NotificationService();
        $service->sendTransactionFailed($transaction, $reason);

        Log::shouldHaveReceived('info')
            ->once()
            ->with(
                "Sent transaction failed notification",
                [
                    'transaction_code' => 'TXN-67890',
                    'type'             => 'transfer',
                    'amount'           => 500,
                    'wallet_id'        => 9,
                    'reason'           => $reason,
                ]
            );
    }
}
