<?php

namespace App\Listeners\Transaction;

use App\Events\Transaction\TransactionCompleted;
use App\Services\NotificationService;

class HandleCompletedTransaction
{
    public function __construct(protected NotificationService $notificationService){}

    public function handle(TransactionCompleted $event): void
    {
        $transaction = $event->transaction;

        // Gửi thông báo
        $this->notificationService->sendTransactionSuccess($transaction);
    }
}