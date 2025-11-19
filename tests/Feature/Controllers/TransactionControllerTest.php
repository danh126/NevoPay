<?php

namespace Tests\Feature\Controllers;

use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $transactionService;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake user login
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Mock TransactionService
        $this->transactionService = Mockery::mock(TransactionService::class);
        $this->app->instance(TransactionService::class, $this->transactionService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    protected function createWallet(string $walletNumber)
    {
        return \App\Models\Wallet::factory()->create([
            'wallet_number' => $walletNumber,
            'user_id' => Auth::id(),
            'is_active' => true,
        ]);
    }

    public function testDepositSuccess()
    {
        $wallet = $this->createWallet('WALLET123');

        $payload = [
            'wallet_number' => $wallet->wallet_number,
            'amount' => 100,
        ];

        $this->transactionService
            ->shouldReceive('deposit')
            ->once()
            ->with($payload['wallet_number'], $payload['amount'], Auth::id())
            ->andReturn([
                'wallet' => ['balance' => 100],
                'transaction' => ['type' => 'deposit', 'amount' => 100]
            ]);

        $response = $this->postJson(route('transactions.deposit'), $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['type' => 'deposit', 'amount' => 100]);
    }

    public function testWithdrawSuccess()
    {
        $wallet = $this->createWallet('WALLET123');

        $payload = [
            'wallet_number' => $wallet->wallet_number,
            'amount' => 50,
        ];

        $this->transactionService
            ->shouldReceive('withdraw')
            ->once()
            ->with($payload['wallet_number'], $payload['amount'], Auth::id())
            ->andReturn([
                'wallet' => ['balance' => 50],
                'transaction' => ['type' => 'withdraw', 'amount' => 50]
            ]);

        $response = $this->postJson(route('transactions.withdraw'), $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['type' => 'withdraw', 'amount' => 50]);
    }

    public function testTransferSuccess()
    {
        $senderWallet = $this->createWallet('WALLET123');
        $receiverWallet = $this->createWallet('WALLET456');

        $payload = [
            'wallet_number' => $senderWallet->wallet_number,
            'to_wallet_number' => $receiverWallet->wallet_number,
            'amount' => 30,
        ];

        $this->transactionService
            ->shouldReceive('transfer')
            ->once()
            ->with($payload['wallet_number'], $payload['to_wallet_number'], $payload['amount'], Auth::id())
            ->andReturn([
                'sender' => ['balance' => 70],
                'receiver' => ['balance' => 130],
                'transaction' => ['type' => 'transfer', 'amount' => 30]
            ]);

        $response = $this->postJson(route('transactions.transfer'), $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['type' => 'transfer', 'amount' => 30]);
    }
}
