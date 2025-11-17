<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo user để authenticate API
        $this->user = User::factory()->create();
    }

    public function test_it_lists_wallets()
    {
        Wallet::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/wallets');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_shows_single_wallet()
    {
        $wallet = Wallet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/wallets/{$wallet->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $wallet->id,
                    'wallet_number' => $wallet->wallet_number,
                ]
            ]);
    }

    public function test_it_returns_404_if_wallet_not_found()
    {
        $response = $this->actingAs($this->user)->getJson("/api/wallets/999");

        $response->assertStatus(404);
    }

    public function test_it_creates_wallet()
    {
        $payload = [
            'user_id' => $this->user->id,
            'currency' => 'VND',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/wallets', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.currency', 'VND')
            ->assertJsonPath('data.balance', '0.00')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->user->id,
            'currency' => 'VND',
        ]);
    }

    public function test_it_validates_wallet_creation()
    {
        $payload = [
            'currency' => '',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/wallets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_it_updates_wallet()
    {
        $wallet = Wallet::factory()->create(['user_id' => $this->user->id]);

        $payload = [
            'is_active' => false,
        ];

        $response = $this->actingAs($this->user)->putJson("/api/wallets/{$wallet->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'is_active' => false,
        ]);
    }

    public function test_it_deletes_wallet()
    {
        $wallet = Wallet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/wallets/{$wallet->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('wallets', [
            'id' => $wallet->id
        ]);
    }

    public function test_it_cannot_delete_wallet_that_does_not_exist()
    {
        $response = $this->actingAs($this->user)->deleteJson("/api/wallets/999");

        $response->assertStatus(404);
    }
}
