<?php

namespace App\Providers;

use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Listeners\Audit\RecordAuditLog;
use App\Listeners\Transaction\HandleCompletedTransaction;
use App\Listeners\Transaction\HandleFailedTransaction;
use App\Listeners\Transaction\ProcessTransaction;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TransactionCreated::class => [
            ProcessTransaction::class,
            RecordAuditLog::class,
        ],

        TransactionCompleted::class => [
            HandleCompletedTransaction::class,
            RecordAuditLog::class,
        ],

        TransactionFailed::class => [
            HandleFailedTransaction::class,
            RecordAuditLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
