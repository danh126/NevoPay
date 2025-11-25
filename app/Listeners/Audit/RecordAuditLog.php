<?php

namespace App\Listeners\Audit;

use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Services\AuditLogService;

class RecordAuditLog
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function handle(object $event): void
    {
        match (true) {
            $event instanceof TransactionCreated   => $this->logTransactionCreated($event),
            $event instanceof TransactionCompleted => $this->logTransactionCompleted($event),
            $event instanceof TransactionFailed    => $this->logTransactionFailed($event),
            default => null,
        };
    }

    protected function logTransactionCreated(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        $this->auditLogService->log(
            action: 'transaction.created',
            model: $transaction,
            oldValues: null,
            newValues: $transaction->toArray(),
            description: 'Transaction created'
        );
    }

    protected function logTransactionCompleted(TransactionCompleted $event): void
    {
        $transaction = $event->transaction;

        $this->auditLogService->log(
            action: 'transaction.completed',
            model: $transaction,
            oldValues: $event->oldValues ?? null,
            newValues: $transaction->toArray(),
            description: 'Transaction completed'
        );
    }

    protected function logTransactionFailed(TransactionFailed $event): void
    {
        $transaction = $event->transaction;

        $this->auditLogService->log(
            action: 'transaction.failed',
            model: $transaction,
            oldValues: $transaction->toArray(),
            newValues: null,
            description: $event->reason ?? 'Transaction failed'
        );
    }
}