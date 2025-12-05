<?php

namespace App\Repositories\Interfaces;

use App\Models\OtpCode;

interface OtpCodeRepositoryInterface
{
    public function createOtp(int $userId, string $channel, string $codeHash, \DateTimeInterface $expiresAt): OtpCode;
    public function getValidOtp(int $userId, string $channel): ?OtpCode;
    public function incrementAttempts(OtpCode $otp): OtpCode;
    public function markAsUsed(OtpCode $otp): OtpCode;
    public function invalidateAllActiveOtps(int $userId, string $channel): void;
}
