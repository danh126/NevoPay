<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService
    ){}

    /** 
     * POST /register
    */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->register($request->validated());

        return ApiResponse::success('User registered successfully.', [
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * POST /login
    */
    public function login(LoginRequest $request)
    {
        try {
            $loginDTO = $this->userService->login($request->validated());

            if ($loginDTO->twoFactorRequired) {
                return ApiResponse::success('Two-factor authentication required.', [
                    'user_id' => $loginDTO->user->id,
                ]);
            }

            return ApiResponse::success('Login successful.', [
                'user' => new UserResource($loginDTO->user),
                'access_token' => $loginDTO->accessToken,
                'token_type' => $loginDTO->tokenType,
            ]);
        } catch (AuthenticationException $e) {
            return ApiResponse::error($e->getMessage(), 401);
        }
    }

    /**
     * POST /verify-2fa
    */
    public function verifyTwoFactor(VerifyTwoFactorRequest $request)
    {
        try {
            $loginDTO = $this->userService->verifyTwoFactor($request->validated('user_id'), $request->validated('otp_code'));

            return ApiResponse::success('Login successful.', [
                'user' => new UserResource($loginDTO->user),
                'access_token' => $loginDTO->accessToken,
                'token_type' => $loginDTO->tokenType,
            ]);
        } catch (AuthenticationException $e) {
            return ApiResponse::error($e->getMessage(), 401);
        }
    }

    /**
     * POST /logout
    */
    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        $this->userService->logout($user, $user->currentAccessToken());
        return ApiResponse::success('Logout successful.'); 
    }
}
