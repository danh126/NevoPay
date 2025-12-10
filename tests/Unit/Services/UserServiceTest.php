<?php

namespace Tests\Unit\Services;

use App\DTO\LoginDTO;
use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\OtpCodeService;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $userRepo;
    protected $walletService;
    protected $otpCodeService;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->walletService = Mockery::mock(WalletService::class);
        $this->otpCodeService = Mockery::mock(OtpCodeService::class);

        $this->service = new UserService(
            $this->userRepo,
            $this->walletService,
            $this->otpCodeService
        );

        Event::fake();
    }

    public function test_registers_user_and_dispatches_event()
    {
        $data = ['email' => 'test@example.com'];

        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->email = 'test@example.com';

        $this->userRepo->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($user);

        $this->walletService->shouldReceive('createForUser')
            ->once()
            ->with($user->id);

        $result = $this->service->register($data);

        $this->assertInstanceOf(User::class, $result);

        Event::assertDispatched(UserRegistered::class);
    }

    public function test_logs_in_successfully_without_2fa()
    {
        $credentials = ['email' => 'TEST@Example.com', 'password' => 'secret'];

        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 10;
        $user->email = 'test@example.com';
        $user->password = Hash::make('secret');
        $user->two_factor_enabled = false;

        $user->shouldReceive('createToken')
            ->once()
            ->andReturn((object) ['plainTextToken' => 'token123']);

        $this->userRepo->shouldReceive('findByEmail')
            ->with($user->email)
            ->andReturn($user);

        $this->userRepo->shouldReceive('isActive')
            ->with(10)
            ->andReturn(true);

        $dto = $this->service->login($credentials);

        $this->assertInstanceOf(LoginDTO::class, $dto);
        $this->assertEquals('token123', $dto->accessToken);

        Event::assertDispatched(UserLoggedIn::class);
    }

    public function test_login_fails_if_user_not_found()
    {
        $this->expectException(AuthenticationException::class);

        $this->userRepo->shouldReceive('findByEmail')->andReturn(null);

        $this->service->login([
            'email' => 'x@example.com',
            'password' => '123',
        ]);
    }

    public function test_login_fails_if_wrong_password()
    {
        $this->expectException(AuthenticationException::class);

        $user = new User(['password' => Hash::make('rightpass')]);

        $this->userRepo->shouldReceive('findByEmail')->andReturn($user);

        $this->service->login([
            'email' => 'x@example.com',
            'password' => 'wrong',
        ]);
    }

    public function test_login_fails_if_user_inactive()
    {
        $this->expectException(AuthenticationException::class);

        $user = new User(['id' => 1, 'password' => Hash::make('123')]);

        $this->userRepo->shouldReceive('findByEmail')->andReturn($user);

        $this->userRepo->shouldReceive('isActive')
            ->with(1)
            ->andReturn(false);

        $this->service->login([
            'email' => 'x@example.com',
            'password' => '123',
        ]);
    }

    public function test_login_requires_2fa_if_enabled()
    {
        $credentials = ['email' => 'test@example.com', 'password' => 'secret'];

        $user = new User([
            'id' => 5,
            'password' => Hash::make('secret'),
            'two_factor_enabled' => true,
        ]);

        $this->userRepo->shouldReceive('findByEmail')->andReturn($user);
        $this->userRepo->shouldReceive('isActive')->andReturn(true);

        $this->otpCodeService->shouldReceive('generateAndSendOtp')
            ->once()
            ->with(5, 'email');

        $dto = $this->service->login($credentials);

        $this->assertTrue($dto->twoFactorRequired);
        $this->assertNull($dto->accessToken);
    }

    public function test_verify_two_factor_success()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 20;

        $user->shouldReceive('createToken')
            ->once()
            ->andReturn((object)['plainTextToken' => 'abc123']);

        $this->userRepo->shouldReceive('findById')->with(20)->andReturn($user);

        $this->otpCodeService->shouldReceive('verifyOtp')
            ->with(20, 'email', '111111')
            ->andReturn(true);

        $dto = $this->service->verifyTwoFactor(20, '111111');

        $this->assertEquals('abc123', $dto->accessToken);

        Event::assertDispatched(UserLoggedIn::class);
    }

    public function test_verify_two_factor_fails_on_wrong_otp()
    {
        $this->expectException(AuthenticationException::class);

        $user = new User(['id' => 30]);

        $this->userRepo->shouldReceive('findById')->with(30)->andReturn($user);

        $this->otpCodeService->shouldReceive('verifyOtp')
            ->with(30, 'email', '123456')
            ->andReturn(false);

        $this->service->verifyTwoFactor(30, '123456');
    }

    public function test_logout_deletes_token_and_fires_event()
    {
        $token = Mockery::mock();
        $token->shouldReceive('delete')->once();

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('currentAccessToken')->andReturn($token);

        $this->service->logout($user);

        Event::assertDispatched(UserLoggedOut::class);
    }
}
