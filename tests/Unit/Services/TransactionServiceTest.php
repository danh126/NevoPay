<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    protected $transactionRepo;
    protected $walletRepo;
    protected $userRepo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $this->walletRepo      = Mockery::mock(WalletRepositoryInterface::class);
        $this->userRepo        = Mockery::mock(UserRepositoryInterface::class);

        $this->service = new TransactionService(
            $this->transactionRepo,
            $this->walletRepo,
            $this->userRepo
        );

        // Fake DB transaction
        DB::shouldReceive('transaction')->andReturnUsing(fn($callback) => $callback());
    }

    private function makeWallet($id, $userId, $balance = 0, $isActive = true)
    {
        $wallet = new Wallet();
        $wallet->id = $id;
        $wallet->user_id = $userId;
        $wallet->balance = $balance;
        $wallet->is_active = $isActive;
        return $wallet;
    }

    private function makeTransaction($id, $amount = 0)
    {
        $transaction = new Transaction();
        $transaction->id = $id;
        $transaction->amount = $amount;
        return $transaction;
    }

    private function mockActiveUser(int $userId)
    {
        $this->userRepo
            ->shouldReceive('isActive')
            ->with($userId)
            ->andReturn(true);
    }

    public function test_deposit_success()
    {
        $walletNumber = 'W001';
        $userId = 1;

        $wallet = $this->makeWallet(10, $userId, 100);
        $updatedWallet = $this->makeWallet(10, $userId, 150);
        $transaction = $this->makeTransaction(1, 50);

        $this->mockActiveUser($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')->with($walletNumber)->andReturn($wallet);
        $this->walletRepo->shouldReceive('find')->with(10)->andReturn($wallet); // owner check
        $this->walletRepo->shouldReceive('updateBalance')->with(10, 50)->andReturn($updatedWallet);
        $this->transactionRepo->shouldReceive('create')->once()->andReturn($transaction);

        $result = $this->service->deposit($walletNumber, 50, $userId);

        $this->assertSame($updatedWallet, $result['wallet']);
        $this->assertSame($transaction, $result['transaction']);
    }

    public function test_deposit_not_owner()
    {
        $walletNumber = 'W001';
        $wallet = $this->makeWallet(10, 2, 100); // owner is 2
        $this->mockActiveUser(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')->with($walletNumber)->andReturn($wallet);
        $this->walletRepo->shouldReceive('find')->with(10)->andReturn($wallet);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You do not own this wallet.");

        $this->service->deposit($walletNumber, 50, 1);
    }

    public function test_withdraw_success()
    {
        $walletNumber = 'W001';
        $userId = 1;

        $wallet = $this->makeWallet(10, $userId, 100);
        $updatedWallet = $this->makeWallet(10, $userId, 50);
        $transaction = $this->makeTransaction(99, 50);

        $this->mockActiveUser($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($wallet);
        $this->walletRepo->shouldReceive('find')->with(10)->andReturn($wallet);
        $this->walletRepo->shouldReceive('hasSufficientBalance')->with(10, 50)->andReturn(true);
        $this->walletRepo->shouldReceive('deductBalance')->with(10, 50)->andReturn($updatedWallet);
        $this->transactionRepo->shouldReceive('create')->once()->andReturn($transaction);

        $result = $this->service->withdraw($walletNumber, 50, $userId);

        $this->assertSame($updatedWallet, $result['wallet']);
        $this->assertSame($transaction, $result['transaction']);
    }

    public function test_withdraw_insufficient_balance()
    {
        $walletNumber = 'W001';
        $userId = 1;
        $wallet = $this->makeWallet(10, $userId, 100);

        $this->mockActiveUser($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($wallet);
        $this->walletRepo->shouldReceive('find')->with(10)->andReturn($wallet);
        $this->walletRepo->shouldReceive('hasSufficientBalance')->andReturn(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Insufficient balance.");

        $this->service->withdraw($walletNumber, 50, $userId);
    }

    public function test_transfer_success()
    {
        $fromWallet = $this->makeWallet(1, 1, 100);
        $toWallet = $this->makeWallet(2, 2, 100);

        $senderResult = $this->makeWallet(1, 1, 50);
        $receiverResult = $this->makeWallet(2, 2, 150);
        $transaction = $this->makeTransaction(123, 50);

        $this->mockActiveUser(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')->with('FROM')->andReturn($fromWallet);
        $this->walletRepo->shouldReceive('findByWalletNumber')->with('TO')->andReturn($toWallet);
        $this->walletRepo->shouldReceive('find')->with(1)->andReturn($fromWallet); // owner check
        $this->walletRepo->shouldReceive('hasSufficientBalance')->with(1, 50)->andReturn(true);
        $this->walletRepo->shouldReceive('deductBalance')->with(1, 50)->andReturn($senderResult);
        $this->walletRepo->shouldReceive('updateBalance')->with(2, 50)->andReturn($receiverResult);
        $this->transactionRepo->shouldReceive('create')->once()->andReturn($transaction);

        $result = $this->service->transfer('FROM', 'TO', 50, 1);

        $this->assertSame($senderResult, $result['sender']);
        $this->assertSame($receiverResult, $result['receiver']);
        $this->assertSame($transaction, $result['transaction']);
    }

    public function test_transfer_same_wallet()
    {
        $wallet = $this->makeWallet(10, 1, 100);
        $this->mockActiveUser(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($wallet);
        $this->walletRepo->shouldReceive('find')->with(10)->andReturn($wallet);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot transfer to the same wallet.");

        $this->service->transfer('W1', 'W1', 50, 1);
    }

    public function test_transfer_insufficient_balance()
    {
        $fromWallet = $this->makeWallet(1, 1, 100);
        $toWallet = $this->makeWallet(2, 2, 200);
        $this->mockActiveUser(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($fromWallet, $toWallet);
        $this->walletRepo->shouldReceive('find')->with(1)->andReturn($fromWallet);
        $this->walletRepo->shouldReceive('hasSufficientBalance')->andReturn(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Insufficient balance.");

        $this->service->transfer('FROM', 'TO', 50, 1);
    }
}
