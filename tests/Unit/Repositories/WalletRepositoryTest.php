<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected WalletRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new WalletRepository();
    }

    public function test_all_returns_all_wallets()
    {
        Wallet::factory()->count(3)->create();
        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_find_returns_wallet_by_id()
    {
        $wallet = Wallet::factory()->create();

        $found = $this->repository->find($wallet->id);

        $this->assertEquals($wallet->id, $found->id);
    }

    public function test_findByWalletNumber_returns_correct_wallet()
    {
        $wallet = Wallet::factory()->create();

        $found = $this->repository->findByWalletNumber($wallet->wallet_number);

        $this->assertEquals($wallet->id, $found->id);
    }

    public function test_findByUserId_returns_correct_wallet()
    {
        $wallet = Wallet::factory()->create();

        $found = $this->repository->findByUserId($wallet->user_id);

        $this->assertEquals($wallet->id, $found->id);
    }

    public function test_getActiveWallets_returns_only_active_wallets()
    {
        Wallet::factory()->create(['is_active' => true]);
        Wallet::factory()->create(['is_active' => false]);

        $active = $this->repository->getActiveWallets();

        $this->assertCount(1, $active);
    }

    public function test_create_creates_wallet_successfully()
    {
        $data = Wallet::factory()->make()->toArray();

        $wallet = $this->repository->create($data);

        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);
    }

    public function test_update_updates_wallet_successfully()
    {
        $wallet = Wallet::factory()->create();
        $updated = $this->repository->update($wallet->id, [
            'balance' => 999
        ]);

        $this->assertEquals(999, $updated->balance);
    }

    public function test_updateBalance_increments_balance_correctly()
    {
        $wallet = Wallet::factory()->create(['balance' => 100]);

        $updated = $this->repository->updateBalance($wallet->id, 50);

        $this->assertEquals(150, $updated->balance);
    }

    public function test_deductBalance_decrements_balance_correctly()
    {
        $wallet = Wallet::factory()->create(['balance' => 200]);

        $updated = $this->repository->deductBalance($wallet->id, 50);

        $this->assertEquals(150, $updated->balance);
    }

    public function test_deductBalance_throws_exception_if_insufficient()
    {
        $wallet = Wallet::factory()->create(['balance' => 20]);

        $this->expectException(\Exception::class);

        $this->repository->deductBalance($wallet->id, 50);
    }

    public function test_delete_removes_wallet()
    {
        $wallet = Wallet::factory()->create();

        $this->repository->delete($wallet->id);

        $this->assertDatabaseMissing('wallets', ['id' => $wallet->id]);
    }

    public function test_isActive_returns_true_if_wallet_is_active()
    {
        $wallet = Wallet::factory()->create(['is_active' => true]);

        $this->assertTrue($this->repository->isActive($wallet->id));
    }

    public function test_hasSufficientBalance_returns_true_when_balance_enough()
    {
        $wallet = Wallet::factory()->create(['balance' => 500]);

        $this->assertTrue($this->repository->hasSufficientBalance($wallet->id, 100));
    }
}
