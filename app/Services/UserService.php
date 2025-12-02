<?php

namespace App\Services;

use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\Auth\UserRegistered;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected UserRepositoryInterface $userRepo, protected WalletService $walletService){}

    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepo->create($data);

            $this->walletService->createForUser($user->id);

            DB::afterCommit(fn () => event(new UserRegistered($user)));

            return $user;
        });
    }

    public function login(array $credentials)
    {
        if (!isset($credentials['email'], $credentials['password']))
            throw new AuthenticationException('Invalid credentials.');

        $user = $this->userRepo->findByEmail($credentials['email']);

        if(!$user || !Hash::check($credentials['password'], $user->password))
            throw new AuthenticationException("Invalid credentials..");

        if(!$this->userRepo->isActive($user->id))
            throw new AuthenticationException("Account is inactive.");

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new UserLoggedIn($user));

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();

        event(new UserLoggedOut($user));
    }
}
