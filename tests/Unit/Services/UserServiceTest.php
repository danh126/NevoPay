<?php

namespace Tests\Unit\Services;

use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Events\Auth\UserLoggedIn;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_register_creates_user_and_wallet_and_dispatches_event()
    {
        Event::fake();

        $user = new User();
        $user->id = 123;

        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $userRepo->shouldReceive('create')->once()->with(['name' => 'Test'])->andReturn($user);

        $walletService = Mockery::mock(WalletService::class);
        $walletService->shouldReceive('createForUser')->once()->with(123);

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($closure) {
            return $closure();
        });

        DB::shouldReceive('afterCommit')->once()->andReturnUsing(function ($cb) {
            $cb();
        });

        $service = new UserService($userRepo, $walletService);

        $result = $service->register(['name' => 'Test']);

        $this->assertSame($user, $result);

        Event::assertDispatched(UserRegistered::class, function ($e) use ($user) {
            return isset($e->user) && $e->user->id === $user->id;
        });
    }

    public function test_login_success_returns_token_and_user()
    {
        $password = 'secret';
        $hashed = 'hashed-password';

        $user = Mockery::mock(User::class);
        $user->password = $hashed;
        $user->id = 1;

        $tokenObj = (object) ['plainTextToken' => 'token123'];
        $user->shouldReceive('createToken')->once()->with('auth_token')->andReturn($tokenObj);

        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $userRepo->shouldReceive('findByEmail')->once()->with('test@example.com')->andReturn($user);
        $userRepo->shouldReceive('isActive')->once()->with(1)->andReturn(true);

        Hash::shouldReceive('check')->once()->with($password, $hashed)->andReturn(true);

        $service = new UserService($userRepo, Mockery::mock(WalletService::class));

        $res = $service->login(['email' => ' test@example.com ', 'password' => $password]);

        $this->assertSame($user, $res['user']);
        $this->assertEquals('token123', $res['access_token']);
        $this->assertEquals('Bearer', $res['token_type']);
    }

    public function test_login_throws_when_missing_credentials()
    {
        $service = new UserService(Mockery::mock(UserRepositoryInterface::class), Mockery::mock(WalletService::class));

        $this->expectException(AuthenticationException::class);

        $service->login(['email' => 'only-email']);
    }

    public function test_login_throws_on_invalid_credentials()
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $userRepo->shouldReceive('findByEmail')->once()->with('notfound@example.com')->andReturn(null);

        $service = new UserService($userRepo, Mockery::mock(WalletService::class));

        $this->expectException(AuthenticationException::class);

        $service->login(['email' => 'notfound@example.com', 'password' => 'x']);
    }

    public function test_logout_deletes_tokens_and_dispatches_event()
    {
        Event::fake();

        $tokens = Mockery::mock();
        $tokens->shouldReceive('delete')->once();

        $user = Mockery::mock(User::class);
        $user->shouldReceive('tokens')->once()->andReturn($tokens);

        $service = new UserService(Mockery::mock(UserRepositoryInterface::class), Mockery::mock(WalletService::class));

        $service->logout($user);

        Event::assertDispatched(UserLoggedOut::class);
    }
}
