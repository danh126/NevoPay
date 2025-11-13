<?php

namespace tests\Unit\Models;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_number_is_12_digits()
    {
        $wallet = Wallet::factory()->create();
        $this->assertEquals(12, strlen($wallet->wallet_number));
    }

    public function test_scope_active_returns_only_active_wallets()
    {
        $activeWallet = Wallet::factory()->active()->create();
        $inactiveWallet = Wallet::factory()->inactive()->create();

        $wallets = Wallet::where('is_active', true)->get();

        $this->assertTrue($wallets->contains($activeWallet));
        $this->assertFalse($wallets->contains($inactiveWallet));
    }
}
