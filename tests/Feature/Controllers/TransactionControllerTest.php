<?php

namespace Tests\Feature\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
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

    protected function createWallet(string $walletNumber): Wallet
    {
        return Wallet::factory()->create([
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

        // Tạo transaction object mock
        $transaction = new Transaction([
            'type' => 'deposit',
            'amount' => 100,
            'wallet_id' => $wallet->id,
        ]);

        $this->transactionService
            ->shouldReceive('deposit')
            ->once()
            ->with($payload['wallet_number'], $payload['amount'], Auth::id())
            ->andReturn($transaction);

        $response = $this->postJson(route('transactions.deposit'), $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Deposit request accepted.',
                     'type' => 'deposit',
                     'amount' => '100.00',
                 ]);
    }

    public function testWithdrawSuccess()
    {
        $wallet = $this->createWallet('WALLET123');

        $payload = [
            'wallet_number' => $wallet->wallet_number,
            'amount' => 50,
        ];

        $transaction = new Transaction([
            'type' => 'withdraw',
            'amount' => 50,
            'wallet_id' => $wallet->id,
        ]);

        $this->transactionService
            ->shouldReceive('withdraw')
            ->once()
            ->with($payload['wallet_number'], $payload['amount'], Auth::id())
            ->andReturn($transaction);

        $response = $this->postJson(route('transactions.withdraw'), $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Withdrawal request accepted.',
                     'type' => 'withdraw',
                     'amount' => '50.00',
                 ]);
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

        $transaction = new Transaction([
            'type' => 'transfer',
            'amount' => 30,
            'wallet_id' => $senderWallet->id,
        ]);

        $this->transactionService
            ->shouldReceive('transfer')
            ->once()
            ->with(
                $payload['wallet_number'],
                $payload['to_wallet_number'],
                $payload['amount'],
                Auth::id()
            )
            ->andReturn($transaction);

        $response = $this->postJson(route('transactions.transfer'), $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Transfer request accepted.',
                     'type' => 'transfer',
                     'amount' => '30.00',
                 ]);
    }
}
