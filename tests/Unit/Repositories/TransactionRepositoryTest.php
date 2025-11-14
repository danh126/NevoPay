<?php

namespace Tests\Unit\Repositories;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new TransactionRepository();
    }

    public function test_create_transaction()
    {
        $transactionData = Transaction::factory()->make()->toArray();
        $transaction = $this->repo->create($transactionData);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
        ]);
    }

    public function test_find_transaction()
    {
        $transaction = Transaction::factory()->create();
        $found = $this->repo->find($transaction->id);

        $this->assertNotNull($found);
        $this->assertEquals($transaction->id, $found->id);
    }

    public function test_find_nonexistent_transaction_returns_null()
    {
        $found = $this->repo->find(999);
        $this->assertNull($found);
    }

    public function test_update_transaction()
    {
        $transaction = Transaction::factory()->create();
        $updateData = ['description' => 'Updated Note'];

        $updated = $this->repo->update($transaction->id, $updateData);

        $this->assertEquals('Updated Note', $updated->description);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Updated Note',
        ]);
    }

    public function test_update_nonexistent_transaction_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repo->update(999, ['note' => 'Fail']);
    }

    public function test_delete_transaction()
    {
        $transaction = Transaction::factory()->create();

        $deleted = $this->repo->delete($transaction->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_delete_nonexistent_transaction_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repo->delete(999);
    }

    public function test_get_by_wallet()
    {
        $wallet = Wallet::factory()->create();
        $transaction = Transaction::factory()->create([
            'sender_wallet_id' => $wallet->id,
        ]);

        $results = $this->repo->getByWallet($wallet->id);
        $collection = collect($results->items());

        $this->assertTrue($collection->contains('id', $transaction->id));
    }

    public function test_get_by_type()
    {
        $transaction = Transaction::factory()->create(['type' => 'deposit']);

        $results = $this->repo->getByType('deposit');
        $collection = collect($results->items());

        $this->assertTrue($collection->contains('id', $transaction->id));
    }

    public function test_filter_transactions()
    {
        $transaction = Transaction::factory()->create([
            'type' => 'transfer',
            'amount' => 500,
        ]);

        $results = $this->repo->filter([
            'type' => 'transfer',
            'min_amount' => 400,
            'max_amount' => 600,
        ]);
        $collection = collect($results->items());

        $this->assertTrue($collection->contains('id', $transaction->id));
    }

    public function test_exists_by_transaction_code()
    {
        $transaction = Transaction::factory()->create();

        $exists = $this->repo->existsByTransactionCode($transaction->transaction_code);
        $this->assertTrue($exists);

        $notExists = $this->repo->existsByTransactionCode('NON_EXISTENT_CODE');
        $this->assertFalse($notExists);
    }

    public function test_get_wallet_summary()
    {
        $wallet = Wallet::factory()->create();
        Transaction::factory()->create([
            'sender_wallet_id' => $wallet->id,
            'amount' => 100,
        ]);
        Transaction::factory()->create([
            'receiver_wallet_id' => $wallet->id,
            'amount' => 300,
        ]);

        $summary = $this->repo->getWalletSummary($wallet->id);

        $this->assertEquals(300, $summary['total_received']);
        $this->assertEquals(100, $summary['total_sent']);
        $this->assertEquals(200, $summary['balance']);
    }
}
