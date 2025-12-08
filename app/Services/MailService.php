<?php

namespace App\Services;

use App\Jobs\SendOtpEmailJob;

class MailService
{
    public function sendOtp(string $email, string $otp, int $expiresInMinutes = 5): void
    {
        // Dispatch job (non-blocking)
        SendOtpEmailJob::dispatch($email, $otp, $expiresInMinutes);
    }
}
