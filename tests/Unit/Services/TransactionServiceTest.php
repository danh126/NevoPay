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

        // Fake DB::transaction
        DB::shouldReceive('transaction')->andReturnUsing(fn($cb) => $cb());
        DB::shouldReceive('afterCommit'); // ignore dispatch listener
    }

    private function makeWallet($id = 10, $userId = 1, $isActive = true)
    {
        $w = new Wallet();
        $w->id = $id;
        $w->user_id = $userId;
        $w->is_active = $isActive;
        return $w;
    }

    private function makeTransaction($id = 1)
    {
        $t = new Transaction();
        $t->id = $id;
        return $t;
    }

    private function mockUserActive($userId)
    {
        $this->userRepo->shouldReceive('isActive')
            ->with($userId)
            ->andReturn(true);
    }

    // --------------------
    // DEPOSIT
    // --------------------

    public function test_deposit_success()
    {
        $userId = 1;
        $walletNumber = 'W001';
        $wallet = $this->makeWallet(10, $userId);

        $this->mockUserActive($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')
            ->with($walletNumber)
            ->andReturn($wallet);

        $this->walletRepo->shouldReceive('assertOwnedBy')
            ->with(10, $userId)
            ->once();

        $transaction = $this->makeTransaction(99);

        $this->transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $result = $this->service->deposit($walletNumber, 50, $userId);

        $this->assertSame($transaction, $result);
    }

    public function test_deposit_user_not_owner()
    {
        $walletNumber = 'W001';
        $wallet = $this->makeWallet(10, 2); // owner = 2

        $this->mockUserActive(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($wallet);

        $this->walletRepo->shouldReceive('assertOwnedBy')
            ->with(10, 1)
            ->andThrow(new InvalidArgumentException("You do not own this wallet."));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You do not own this wallet.");

        $this->service->deposit($walletNumber, 50, 1);
    }

    // --------------------
    // WITHDRAW
    // --------------------

    public function test_withdraw_success()
    {
        $userId = 1;
        $walletNumber = 'W001';
        $wallet = $this->makeWallet(10, $userId);

        $this->mockUserActive($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')->andReturn($wallet);
        $this->walletRepo->shouldReceive('assertOwnedBy')->with(10, $userId);

        $transaction = $this->makeTransaction(55);

        $this->transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $result = $this->service->withdraw($walletNumber, 50, $userId);

        $this->assertSame($transaction, $result);
    }

    // --------------------
    // TRANSFER
    // --------------------

    public function test_transfer_success()
    {
        $userId = 1;
        $fromWallet = $this->makeWallet(10, $userId);
        $toWallet   = $this->makeWallet(20, 2);

        $this->mockUserActive($userId);

        $this->walletRepo->shouldReceive('findByWalletNumber')
            ->with('FROM')->andReturn($fromWallet);

        $this->walletRepo->shouldReceive('findByWalletNumber')
            ->with('TO')->andReturn($toWallet);

        $this->walletRepo->shouldReceive('assertOwnedBy')->with(10, $userId);

        $transaction = $this->makeTransaction(77);

        $this->transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $result = $this->service->transfer('FROM', 'TO', 50, $userId);

        $this->assertSame($transaction, $result);
    }

    public function test_transfer_same_wallet()
    {
        $wallet = $this->makeWallet(10, 1);
        $this->mockUserActive(1);

        $this->walletRepo->shouldReceive('findByWalletNumber')
            ->andReturn($wallet);

        // gọi 2 lần cho both from/to
        $this->walletRepo->shouldReceive('findByWalletNumber')
            ->andReturn($wallet);

        $this->walletRepo->shouldReceive('assertOwnedBy')
            ->with(10, 1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot transfer to the same wallet.");

        $this->service->transfer('W1', 'W1', 50, 1);
    }
}
