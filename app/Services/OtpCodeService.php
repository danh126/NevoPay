<?php

namespace App\Services;

use App\DTO\GenerateOtpDTO;
use App\Repositories\Interfaces\OtpCodeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OtpCodeService
{
    public function __construct(protected OtpCodeRepositoryInterface $otpRepo){}

    /**
     * Generate OTP
     */
    public function generateOtp(int $userId, string $channel): GenerateOtpDTO
    {
        $channel = $this->validateChannel($channel);
        $secret = $this->validateSecretKey();

        $otp = random_int(100000, 999999);

        $hash = hash('sha256', $otp . $secret);

        $expiresAt = Carbon::now()->addMinutes(config('otp.ttl', 5));

        $record = $this->otpRepo->createOtp(
            $userId,
            $channel,
            $hash,
            $expiresAt
        );

        return new GenerateOtpDTO(
            otp_code: (string) $otp,
            otp_record: $record,
        );
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(int $userId, string $channel, string $inputOtp): bool
    {
        $channel = $this->validateChannel($channel);
        $secret = $this->validateSecretKey();
        
        $otp = $this->otpRepo->getValidOtp($userId, $channel);

        if (!$otp) {
            return false;
        }

        if ($otp->attempts >= config('otp.max_attempts', 3)) {
            $this->otpRepo->markAsUsed($otp);
            return false;
        }

        $computedHash = hash('sha256', $inputOtp . $secret);

        if (!hash_equals($otp->code_hash, $computedHash)) {
            $this->otpRepo->incrementAttempts($otp);
            return false;
        }

        DB::transaction(function () use ($otp, $userId, $channel) {
            $this->otpRepo->markAsUsed($otp);
            $this->otpRepo->invalidateAllActiveOtps($userId, $channel);  
        });
        
        return true;
    }

    /**
     * Validate secret key
     */
    private function validateSecretKey(): string
    {
        $secret = config('otp.secret');

        if (empty($secret)) {
            throw new \RuntimeException('OTP secret key is not configured.');
        }

        return $secret;
    }

    /**
     * Validate channel
     */
    private function validateChannel(string $channel): string
    {
        $validChannels = ['email', 'sms', 'authenticator_app'];

        $channel = strtolower($channel);

        if (!in_array($channel, $validChannels)) {
            throw new \InvalidArgumentException('Invalid OTP channel specified.');
        }

        return $channel;
    }

}
