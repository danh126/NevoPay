<?php

namespace App\Listeners\Transaction;

use App\Events\Transaction\TransactionFailed;
use App\Services\NotificationService;

class HandleFailedTransaction
{
    public function __construct(protected NotificationService $notificationService){}

    public function handle(TransactionFailed $event): void
    {
        $transaction = $event->transaction;

        $this->notificationService->sendTransactionFailed(
            $transaction,
            $event->reason
        );
    }
}