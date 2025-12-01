<?php

namespace Tests\Unit\Listeners;

use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Listeners\Transaction\ProcessTransaction;
use App\Models\Transaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class ProcessTransactionTest extends TestCase
{
    protected $walletRepo;
    protected $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepo = Mockery::mock(WalletRepositoryInterface::class);
        $this->listener = new ProcessTransaction($this->walletRepo);

        Event::fake();

        // Fake DB transaction() và afterCommit
        DB::shouldReceive('transaction')->andReturnUsing(fn ($callback) => $callback());
        DB::shouldReceive('afterCommit')->andReturnUsing(fn ($callback) => $callback());
    }

    /** Create transaction instance without touching DB */
    private function makeTransaction(array $override = [])
    {
        /** @var Transaction|\Mockery\MockInterface $transaction */
        $transaction = \Mockery::mock(Transaction::class)->makePartial();

        $transaction->fill(array_merge([
            'id'                => 1,
            'wallet_id'         => 10,
            'sender_wallet_id'  => null,
            'receiver_wallet_id'=> null,
            'type'              => 'deposit',
            'amount'            => 1000,
            'status'            => 'pending',
            'description'       => 'test',
            'created_by'        => 1,
            'completed_at'      => null,
        ], $override));

        $transaction->exists = true;

        $transaction->shouldReceive('fresh')->andReturnSelf();

        return $transaction;
    }

    public function test_it_processes_deposit_successfully()
    {
        $transaction = $this->makeTransaction([
            'type'   => 'deposit',
            'amount' => 1000,
            'status' => 'pending',
        ]);

        // Expect repo gọi updateBalance
        $this->walletRepo
            ->shouldReceive('updateBalance')
            ->once()
            ->with($transaction->wallet_id, 1000);

        // Expect model update status = completed
        $transaction->shouldReceive('update')
            ->once()
            ->with(Mockery::on(fn ($arg) => $arg['status'] === 'completed'));

        $event = new TransactionCreated($transaction);

        $this->listener->handle($event);

        Event::assertDispatched(TransactionCompleted::class);
    }

    public function test_it_processes_withdraw_successfully()
    {
        $transaction = $this->makeTransaction([
            'type'   => 'withdraw',
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->walletRepo
            ->shouldReceive('hasSufficientBalance')
            ->once()
            ->with($transaction->wallet_id, 500)
            ->andReturn(true);

        $this->walletRepo
            ->shouldReceive('deductBalance')
            ->once()
            ->with($transaction->wallet_id, 500);

        $transaction->shouldReceive('update')->once();

        $event = new TransactionCreated($transaction);
        $this->listener->handle($event);

        Event::assertDispatched(TransactionCompleted::class);
    }

    public function test_it_processes_transfer_successfully()
    {
        $transaction = $this->makeTransaction([
            'sender_wallet_id'  => 10,
            'receiver_wallet_id'=> 20,
            'type'   => 'transfer',
            'amount' => 300,
            'status' => 'pending',
        ]);

        $this->walletRepo
            ->shouldReceive('hasSufficientBalance')
            ->once()
            ->with(10, 300)
            ->andReturn(true);

        $this->walletRepo
            ->shouldReceive('deductBalance')
            ->once()
            ->with(10, 300);

        $this->walletRepo
            ->shouldReceive('updateBalance')
            ->once()
            ->with(20, 300);

        $transaction->shouldReceive('update')->once();

        $event = new TransactionCreated($transaction);
        $this->listener->handle($event);

        Event::assertDispatched(TransactionCompleted::class);
    }

    public function test_it_handles_failed_transaction()
    {
        $transaction = $this->makeTransaction([
            'type'   => 'withdraw',
            'amount' => 1000,
            'status' => 'pending',
        ]);

        // Force fail
        $this->walletRepo
            ->shouldReceive('hasSufficientBalance')
            ->once()
            ->andReturn(false);

        // Expect fail update
        $transaction->shouldReceive('update')
            ->once()
            ->with(Mockery::on(fn ($arg) => $arg['status'] === 'failed'));

        $event = new TransactionCreated($transaction);

        $this->listener->handle($event);

        Event::assertDispatched(TransactionFailed::class);
    }
}
