<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_hashed_automatically()
    {
        $user = User::factory()->create();
        $this->assertTrue(Hash::check('demo12345', $user->password));
    }

    public function test_scope_active_returns_only_active_users()
    {
        $activeUser = User::factory()->active()->create();
        $inactiveUser = User::factory()->inactive()->create();

        $users = User::where('is_active', true)->get();

        $this->assertTrue($users->contains($activeUser));
        $this->assertFalse($users->contains($inactiveUser));
    }

    public function test_user_has_wallet_relation()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($wallet->id, $user->wallet->id);
    }
}
