<?php

namespace App\Providers;

use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Listeners\Audit\RecordAuditLog;
use App\Listeners\Transaction\HandleCompletedTransaction;
use App\Listeners\Transaction\HandleFailedTransaction;
use App\Listeners\Transaction\ProcessTransaction;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Transaction Events
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

        // User Events
        UserRegistered::class => [
            RecordAuditLog::class,
        ],

        UserLoggedIn::class => [
            RecordAuditLog::class,
        ],

        UserLoggedOut::class => [
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
