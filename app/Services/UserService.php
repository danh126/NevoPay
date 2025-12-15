<?php

namespace App\Services;

use App\DTO\LoginDTO;
use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo, 
        protected WalletService $walletService, 
        protected OtpCodeService $otpCodeService
    ){}

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepo->create($data);

            $this->walletService->createForUser($user->id);

            DB::afterCommit(fn () => event(new UserRegistered($user)));

            return $user;
        });
    }

    public function login(array $credentials): LoginDTO
    {
        $this->validateCredentials($credentials);
        $email = strtolower(trim($credentials['email']));

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (!$this->userRepo->isActive($user->id)) {
            throw new AuthenticationException('Account is inactive.');
        }

        // Check if 2FA is enabled
        if($user->two_factor_enabled) {
            $this->otpCodeService->generateAndSendOtp($user->id, 'email');

            return new LoginDTO(
                user: $user,
                accessToken: null,
                tokenType: null,
                twoFactorRequired: true,
            );
        }

        // If 2FA is not enabled, proceed to create token
        $token = $user->createToken('auth_token', ['*'] ,\now()->addDays(3))->plainTextToken;

        event(new UserLoggedIn($user));

        return new LoginDTO(
            user: $user,
            accessToken: $token,
        );
    }

    public function verifyTwoFactor(int $userId, string $inputOtp): LoginDTO
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        // Verify OTP
        $isValid = $this->otpCodeService->verifyOtp($user->id, 'email', $inputOtp);

        if (!$isValid) {
            throw new AuthenticationException('Invalid or expired OTP code.');
        }

        // Generate auth token
        $token = $user->createToken('auth_token', ['*'] ,\now()->addDays(3))->plainTextToken;

        event(new UserLoggedIn($user));

        return new LoginDTO(
            user: $user,
            accessToken: $token,
        );
    }

    public function logout(User $user, ?PersonalAccessToken $token): void
    {
        $token?->delete();
        
        event(new UserLoggedOut($user));
    }

    private function validateCredentials(array $credentials): void
    {
        if (!isset($credentials['email'], $credentials['password'])) {
            throw new AuthenticationException('Invalid credentials.');
        }
    }
}
