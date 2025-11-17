<?php

namespace Tests\Unit\Services;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\WalletService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    protected $walletRepositoryMock;
    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletRepositoryMock = Mockery::mock(WalletRepositoryInterface::class);
        $this->walletService = new WalletService($this->walletRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_wallet_for_user()
    {
        $userId = 1;
        $data = ['currency' => 'VND'];

        $wallet = new Wallet([
            'id' => 1,
            'user_id' => $userId,
            'balance' => 0,
            'currency' => 'VND',
            'is_active' => true
        ]);

        $this->walletRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($userId) {
                return $arg['user_id'] === $userId &&
                       isset($arg['balance']) &&
                       isset($arg['currency']);
            }))
            ->andReturn($wallet);

        $result = $this->walletService->createForUser($userId, $data);

        $this->assertInstanceOf(Wallet::class, $result);
        $this->assertEquals($userId, $result->user_id);
        $this->assertEquals('VND', $result->currency);
        $this->assertEquals(0, $result->balance);
    }

    public function test_it_gets_wallet_by_id()
    {
        $walletId = 1;
        $wallet = new Wallet(['id' => $walletId]);

        $this->walletRepositoryMock
            ->shouldReceive('find')
            ->once()
            ->with($walletId)
            ->andReturn($wallet);

        $result = $this->walletService->getWallet($walletId);

        $this->assertSame($wallet, $result);
    }

    public function test_it_throws_exception_if_wallet_not_found()
    {
        $this->walletRepositoryMock
            ->shouldReceive('find')
            ->once()
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->walletService->getWallet(999);
    }

    public function test_it_updates_wallet_without_balance_and_wallet_number()
    {
        $walletId = 1;
        $data = ['balance' => 100, 'wallet_number' => 'WALLET999', 'currency' => 'USD'];

        $updatedWallet = new Wallet([
            'id' => $walletId,
            'balance' => 0,
            'wallet_number' => 'WALLET123',
            'currency' => 'USD',
            'is_active' => true
        ]);

        $this->walletRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($walletId, ['currency' => 'USD'])
            ->andReturn($updatedWallet);

        $result = $this->walletService->update($walletId, $data);

        $this->assertEquals('USD', $result->currency);
        $this->assertEquals(0, $result->balance);
        $this->assertEquals('WALLET123', $result->wallet_number);
    }

    public function test_it_toggles_wallet_active_status()
    {
        $walletId = 1;
        $status = true;

        $wallet = new Wallet(['id' => $walletId, 'is_active' => $status]);

        $this->walletRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($walletId, ['is_active' => $status])
            ->andReturn($wallet);

        $result = $this->walletService->toggleActive($walletId, $status);

        $this->assertTrue($result->is_active);
    }

    public function test_it_deletes_wallet()
    {
        $walletId = 1;

        $this->walletRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($walletId)
            ->andReturn(true);

        $result = $this->walletService->delete($walletId);

        $this->assertTrue($result);
    }

    public function test_it_gets_active_wallets()
    {
        $wallets = collect([
            new Wallet(['id' => 1]),
            new Wallet(['id' => 2])
        ]);

        $this->walletRepositoryMock
            ->shouldReceive('getActiveWallets')
            ->once()
            ->andReturn($wallets);

        $result = $this->walletService->getActiveWallets();

        $this->assertCount(2, $result);
    }

    public function test_it_checks_if_wallet_is_active()
    {
        $walletId = 1;

        $this->walletRepositoryMock
            ->shouldReceive('isActive')
            ->once()
            ->with($walletId)
            ->andReturn(true);

        $result = $this->walletService->isActive($walletId);

        $this->assertTrue($result);
    }

    public function test_it_gets_wallet_by_wallet_number()
    {
        $walletNumber = 'WALLET123';
        $wallet = new Wallet(['wallet_number' => $walletNumber]);

        $this->walletRepositoryMock
            ->shouldReceive('findByWalletNumber')
            ->once()
            ->with($walletNumber)
            ->andReturn($wallet);

        $result = $this->walletService->getByWalletNumber($walletNumber);

        $this->assertSame($wallet, $result);
    }
}
