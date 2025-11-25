<?php

namespace Tests\Unit\Listeners;

use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Listeners\Audit\RecordAuditLog;
use App\Models\Transaction;
use App\Services\AuditLogService;
use Mockery;
use Tests\TestCase;

class RecordAuditLogTest extends TestCase
{
    protected $auditLogService;
    protected $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditLogService = Mockery::mock(AuditLogService::class);
        $this->listener = new RecordAuditLog($this->auditLogService);
    }

    public function test_transaction_created_event_is_logged()
    {
        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('toArray')->andReturn(['id' => 1]);

        $event = new TransactionCreated($transaction);

        $this->auditLogService
            ->shouldReceive('log')
            ->once()
            ->with(
                'transaction.created',
                $transaction,
                null,
                ['id' => 1],
                'Transaction created'
            );

        $this->listener->handle($event);
    }

    public function test_transaction_completed_event_is_logged()
    {
        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('toArray')->andReturn(['id' => 1]);

        $event = new TransactionCompleted(
            transaction: $transaction,
            oldValues: ['status' => 'pending']
        );

        $this->auditLogService
            ->shouldReceive('log')
            ->once()
            ->with(
                'transaction.completed',
                $transaction,
                ['status' => 'pending'],
                ['id' => 1],
                'Transaction completed'
            );

        $this->listener->handle($event);
    }

    public function test_transaction_failed_event_is_logged()
    {
        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('toArray')->andReturn(['id' => 1]);

        $event = new TransactionFailed(
            transaction: $transaction,
            reason: 'Insufficient funds'
        );

        $this->auditLogService
            ->shouldReceive('log')
            ->once()
            ->with(
                'transaction.failed',
                $transaction,
                ['id' => 1],
                null,
                'Insufficient funds'
            );

        $this->listener->handle($event);
    }

    public function test_unhandled_event_does_not_trigger_logging()
    {
        $event = new class {};

        $this->auditLogService->shouldNotReceive('log');

        $this->listener->handle($event);

        $this->assertTrue(true);
    }
}
