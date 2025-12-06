<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public string $otp;
    public int $expiresInMinutes;

    public int $tries = 3; // Number of attempts
    public int $timeout = 120; // Timeout for the job in seconds
    public array $backoff = [10, 20, 60]; // Delay between retries in seconds

    public function __construct(string $email, string $otp, int $expiresInMinutes = 5)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function handle() : void
    {
        try {
            $mail = $this->buildMail();
            Mail::to($this->email)->send($mail);
        } catch (\Throwable $e) {
            Log::error('SendOtpEmailJob failed during handle()', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Rethrow to trigger retry
        }
    }

    public function failed(\Throwable $e) : void
    {
        // Ghi log, dispatch event, or notify admin
        Log::error('SendOtpEmailJob failed', [
            'email' => $this->email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'job_id' => $this->job?->getJobId(),
        ]);
    }

    protected function buildMail(): OtpMail
    {
        return new OtpMail($this->otp, $this->expiresInMinutes);
    }
}
