<?php

namespace Tests\Unit\Listeners;

use App\Events\Transaction\TransactionCompleted;
use App\Listeners\Transaction\HandleCompletedTransaction;
use App\Models\Transaction;
use App\Services\NotificationService;
use Mockery;
use Tests\TestCase;

class HandleCompletedTransactionTest extends TestCase
{
    public function test_it_sends_notification_on_completed_transaction()
    {
        // Fake transaction model (không cần DB)
        $transaction = new Transaction([
            'id'     => 1,
            'amount' => 1000,
            'status' => 'completed',
        ]);
        $transaction->exists = true;

        // Mock NotificationService
        $notificationService = Mockery::mock(NotificationService::class);

        // Expect it to be called once with the transaction
        $notificationService
            ->shouldReceive('sendTransactionSuccess')
            ->once()
            ->with($transaction);

        // Instantiate listener
        $listener = new HandleCompletedTransaction($notificationService);

        // Fire event
        $event = new TransactionCompleted($transaction);
        $listener->handle($event);
    }
}
