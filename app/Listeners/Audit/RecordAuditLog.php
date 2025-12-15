<?php

namespace App\Listeners\Audit;

use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Services\AuditLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordAuditLog implements ShouldQueue 
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        protected AuditLogService $audit
    ){}
    
    public function handle(object $event): void
    {
        match (true) {
            // Transaction Events
            $event instanceof TransactionCreated   => $this->logTransactionCreated($event),
            $event instanceof TransactionCompleted => $this->logTransactionCompleted($event),
            $event instanceof TransactionFailed    => $this->logTransactionFailed($event),

            // User Events
            $event instanceof UserRegistered       => $this->logUserRegistered($event),
            $event instanceof UserLoggedIn         => $this->logUserLoggedIn($event),
            $event instanceof UserLoggedOut        => $this->logUserLoggedOut($event),

            default => logger('Audit skipped: unsupported event', [
                'event' => get_class($event),
            ]),
        };
    }

    // -------------------------------
    // Transaction logging
    // -------------------------------
    protected function logTransactionCreated(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        $this->audit->log(
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

        $this->audit->log(
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

        $this->audit->log(
            action: 'transaction.failed',
            model: $transaction,
            oldValues: $transaction->toArray(),
            newValues: null,
            description: $event->reason ?? 'Transaction failed'
        );
    }

    // -------------------------------
    // User logging
    // -------------------------------
    protected function logUserRegistered(UserRegistered $event): void
    {
        $user = $event->user;

        $this->audit->log(
            userId: $user->id,
            action: 'user.registered',
            model: $user,
            oldValues: null,
            newValues: $user->toArray(),
            description: 'User registered'
        );
    }

    protected function logUserLoggedIn(UserLoggedIn $event): void
    {
        $user = $event->user;

        $this->audit->log(
            userId: $user->id,
            action: 'user.logged_in',
            model: $user,
            oldValues: null,
            newValues: null,
            description: 'User logged in'
        );
    }

    protected function logUserLoggedOut(UserLoggedOut $event): void
    {
        $user = $event->user;

        $this->audit->log(
            userId: $user->id,
            action: 'user.logged_out',
            model: $user,
            oldValues: null,
            newValues: null,
            description: 'User logged out'
        );
    }

    // Override failed method to log exceptions
    public function failed(\Throwable $e): void
    {
        logger()->error('Audit log failed', [
            'exception' => $e->getMessage(),
        ]);
    }
}