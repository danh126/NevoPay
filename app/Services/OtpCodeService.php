<?php

namespace App\Services;

use App\DTO\GenerateOtpDTO;
use App\Repositories\Interfaces\OtpCodeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OtpCodeService
{
    public function __construct(protected OtpCodeRepositoryInterface $otpCodeRepo, protected MailService $mailService){}

    /**
     * Generate and send OTP
     */
    public function generateAndSendOtp(int $userId, string $channel): GenerateOtpDTO
    {
        $channel = $this->validateChannel($channel);
        $secret = $this->validateSecretKey();
        $ttl = config('otp.ttl', 5);

        $otp = random_int(100000, 999999);

        $hash = hash('sha256', $otp . $secret);

        $expiresAt = Carbon::now()->addMinutes($ttl);

        $record = $this->otpCodeRepo->createOtp(
            $userId,
            $channel,
            $hash,
            $expiresAt
        );

        $this->mailService->sendOtp(
            email: $record->user->email,
            otp: (string) $otp,
            expiresInMinutes: $ttl,
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
        
        $otp = $this->otpCodeRepo->getValidOtp($userId, $channel);

        if (!$otp) {
            return false;
        }

        if ($otp->attempts >= config('otp.max_attempts', 3)) {
            $this->otpCodeRepo->markAsUsed($otp);
            return false;
        }

        $computedHash = hash('sha256', $inputOtp . $secret);

        if (!hash_equals($otp->code_hash, $computedHash)) {
            $this->otpCodeRepo->incrementAttempts($otp);
            return false;
        }

        DB::transaction(function () use ($otp, $userId, $channel) {
            $this->otpCodeRepo->markAsUsed($otp);
            $this->otpCodeRepo->invalidateAllActiveOtps($userId, $channel);  
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
