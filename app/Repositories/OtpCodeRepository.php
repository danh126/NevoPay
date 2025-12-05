<?php

namespace App\Repositories;

use App\Models\OtpCode;
use App\Repositories\Interfaces\OtpCodeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OtpCodeRepository implements OtpCodeRepositoryInterface
{
    public function createOtp(int $userId, string $channel, string $codeHash, \DateTimeInterface $expiresAt): OtpCode
    {
        return DB::transaction(function () use ($userId, $channel, $codeHash, $expiresAt) {
            // Disable any existing active OTPs for the user and channel
            OtpCode::where('user_id', $userId)
                ->where('channel', $channel)
                ->where('is_used', false)
                ->update(['is_used' => true]);

            // Create and return the new OTP record
            return OtpCode::create([
                'user_id' => $userId,
                'channel' => $channel,
                'code_hash' => $codeHash,
                'expires_at' => $expiresAt,
                'attempts' => 0,
                'is_used' => false,
            ]);
        });
    }

    public function getValidOtp(int $userId, string $channel): ?OtpCode
    {
        return OtpCode::where('user_id', $userId)
            ->where('channel', $channel)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();
    }

    public function incrementAttempts(OtpCode $otp): OtpCode
    {
        $otp->increment('attempts');
        return $otp->fresh();
    }

    public function markAsUsed(OtpCode $otp): OtpCode
    {
        $otp->update(['is_used' => true]);
        return $otp->fresh();
    }

    public function invalidateAllActiveOtps(int $userId, string $channel): void
    {
        OtpCode::where('user_id', $userId)
            ->where('channel', $channel)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->update(['is_used' => true]);
    }
}
