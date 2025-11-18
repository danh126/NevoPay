<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    protected $transactionRepository;
    protected $walletRepository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $this->service = new TransactionService(
            $this->transactionRepository,
            $this->walletRepository
        );
    }

    public function test_deposit_success()
    {
        $walletCode = "W123";
        $amount = 100;
        $createdBy = 1;

        $wallet = new Wallet([
            'id' => 10,
            'wallet_number' => $walletCode,
            'balance' => 500
        ]);

        $transaction = new Transaction([
            'id' => 1,
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'deposit',
            'status' => 'completed'
        ]);

        // ensure findByWalletNumber returns wallet for the given code
        $this->walletRepository
            ->expects($this->once())
            ->method('findByWalletNumber')
            ->with($walletCode)
            ->willReturn($wallet);

        $this->walletRepository
            ->expects($this->once())
            ->method('updateBalance')
            ->with($wallet->id, $amount)
            ->willReturn($wallet);

        $this->transactionRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($transaction);

        $result = $this->service->deposit($walletCode, $amount, $createdBy);

        $this->assertEquals($wallet, $result['wallet']);
        $this->assertEquals($transaction, $result['transaction']);
    }

    public function test_deposit_invalid_amount()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->deposit("W123", 0, 1);
    }

    public function test_withdraw_success()
    {
        $walletCode = "W123";
        $amount = 50;
        $createdBy = 1;

        $wallet = new Wallet([
            'id' => 5,
            'wallet_number' => $walletCode,
            'balance' => 200
        ]);

        $transaction = new Transaction([
            'id' => 2,
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'withdraw',
            'status' => 'completed'
        ]);

        // findByWalletNumber must return wallet
        $this->walletRepository
            ->expects($this->once())
            ->method('findByWalletNumber')
            ->with($walletCode)
            ->willReturn($wallet);

        $this->walletRepository
            ->expects($this->once())
            ->method('hasSufficientBalance')
            ->with($wallet->id, $amount)
            ->willReturn(true);

        $this->walletRepository
            ->expects($this->once())
            ->method('deductBalance')
            ->with($wallet->id, $amount)
            ->willReturn($wallet);

        $this->transactionRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($transaction);

        $result = $this->service->withdraw($walletCode, $amount, $createdBy);

        $this->assertEquals($wallet, $result['wallet']);
        $this->assertEquals($transaction, $result['transaction']);
    }

    public function test_withdraw_insufficient_balance()
    {
        $walletCode = 'W123';
        $amount = 100;
        $createdBy = 1;

        $wallet = new Wallet([
            'id' => 3,
            'wallet_number' => $walletCode,
            'balance' => 50
        ]);

        $this->walletRepository
            ->expects($this->once())
            ->method('findByWalletNumber')
            ->with($walletCode)
            ->willReturn($wallet);

        $this->walletRepository
            ->expects($this->once())
            ->method('hasSufficientBalance')
            ->with($wallet->id, $amount)
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $this->service->withdraw($walletCode, $amount, $createdBy);
    }

    public function test_transfer_success()
    {
        $fromCode = "W111";
        $toCode = "W222";
        $amount = 40;
        $createdBy = 1;

        $fromWallet = new Wallet([
            'id' => 1,
            'wallet_number' => $fromCode,
            'balance' => 300
        ]);

        $toWallet = new Wallet([
            'id' => 2,
            'wallet_number' => $toCode,
            'balance' => 100
        ]);

        $transaction = new Transaction([
            'id' => 3,
            'wallet_id' => $fromWallet->id,
            'amount' => $amount,
            'type' => 'transfer',
            'status' => 'completed',
            'sender_wallet_id' => $fromWallet->id,
            'receiver_wallet_id' => $toWallet->id,
        ]);

        // Use callback to return correct wallet by code (fixes null return)
        $this->walletRepository
            ->expects($this->exactly(2))
            ->method('findByWalletNumber')
            ->willReturnOnConsecutiveCalls($fromWallet, $toWallet);

        $this->walletRepository
            ->expects($this->once())
            ->method('hasSufficientBalance')
            ->with($fromWallet->id, $amount)
            ->willReturn(true);

        $this->walletRepository
            ->expects($this->once())
            ->method('deductBalance')
            ->with($fromWallet->id, $amount)
            ->willReturn($fromWallet);

        $this->walletRepository
            ->expects($this->once())
            ->method('updateBalance')
            ->with($toWallet->id, $amount)
            ->willReturn($toWallet);

        $this->transactionRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($transaction);

        $result = $this->service->transfer($fromCode, $toCode, $amount, $createdBy);

        $this->assertEquals($fromWallet, $result['sender']);
        $this->assertEquals($toWallet, $result['receiver']);
        $this->assertEquals($transaction, $result['transaction']);
    }

    public function test_transfer_same_wallet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot transfer to the same wallet.");

        $this->service->transfer("W123", "W123", 100, 1);
    }

    public function test_resolveWalletIdFromCode_invalid_wallet()
    {
        $this->walletRepository
            ->expects($this->once())
            ->method('findByWalletNumber')
            ->willThrowException(new ModelNotFoundException());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Wallet code 'INVALID' is invalid.");

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('resolveWalletIdFromCode');
        $method->setAccessible(true);

        $method->invoke($this->service, 'INVALID');
    }
}
