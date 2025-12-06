<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public int $expiresInMinutes;

    public function __construct(string $otp, int $expiresInMinutes = 5)
    {
        $this->otp = $otp;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function build() : static
    {
        return $this->subject('Mã OTP cho NevoPay')
                    ->markdown('emails.otp')
                    ->with([
                        'otp' => $this->otp,
                        'expiresIn' => $this->expiresInMinutes,
                    ])
                    ->priority(1);
    }
}
