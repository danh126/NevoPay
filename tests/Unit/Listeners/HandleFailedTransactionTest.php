<?php

namespace Tests\Unit\Listeners;

use App\Events\Transaction\TransactionFailed;
use App\Listeners\Transaction\HandleFailedTransaction;
use App\Models\Transaction;
use App\Services\NotificationService;
use Mockery;
use Tests\TestCase;

class HandleFailedTransactionTest extends TestCase
{
    public function test_it_sends_notification_on_failed_transaction()
    {
        // Fake transaction model (không cần DB)
        $transaction = new Transaction([
            'id'     => 1,
            'amount' => 1000,
            'status' => 'failed',
        ]);
        $transaction->exists = true;

        $reason = 'Insufficient balance';

        // Mock NotificationService
        $notificationService = Mockery::mock(NotificationService::class);

        // Expect it to be called once with the transaction
        $notificationService
            ->shouldReceive('sendTransactionFailed')
            ->once()
            ->with($transaction, $reason);

        // Instantiate listener
        $listener = new HandleFailedTransaction($notificationService);

        // Fire event
        $event = new TransactionFailed($transaction, $reason);
        $listener->handle($event);
    }
}
