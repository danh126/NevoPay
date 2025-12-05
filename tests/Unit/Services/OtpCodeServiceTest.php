<?php

namespace Tests\Unit\Services;

use App\DTO\GenerateOtpDTO;
use App\Models\OtpCode;
use App\Repositories\Interfaces\OtpCodeRepositoryInterface;
use App\Services\OtpCodeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OtpCodeServiceTest extends TestCase
{
    protected $otpRepo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->otpRepo = $this->createMock(OtpCodeRepositoryInterface::class);
        $this->service = new OtpCodeService($this->otpRepo);

        // Set fake secret
        Config::set('otp.secret', 'FAKE_SECRET_KEY');
        Config::set('otp.ttl', 5);
        Config::set('otp.max_attempts', 3);
    }

    public function test_generates_otp_correctly()
    {
        $userId = 1;
        $channel = 'email';

        $fakeOtpRecord = new OtpCode([
            'user_id' => $userId,
            'channel' => $channel,
            'code_hash' => 'xxxx',
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_used' => false,
        ]);

        $this->otpRepo->expects($this->once())
            ->method('createOtp')
            ->with(
                $this->equalTo($userId),
                $this->equalTo('email'),
                $this->isType('string'),
                $this->isInstanceOf(Carbon::class)
            )
            ->willReturn($fakeOtpRecord);

        $result = $this->service->generateOtp($userId, $channel);

        $this->assertInstanceOf(GenerateOtpDTO::class, $result);
        $this->assertIsString($result->otp_code);
        $this->assertInstanceOf(OtpCode::class,  $result->otp_record);
    }

    public function test_verifies_valid_otp()
    {
        $userId = 1;
        $channel = 'email';
        $inputOtp = '123456';

        $hashed = hash('sha256', $inputOtp . 'FAKE_SECRET_KEY');

        $otp = new OtpCode([
            'user_id' => $userId,
            'channel' => 'email',
            'code_hash' => $hashed,
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_used' => false,
        ]);

        $this->otpRepo->method('getValidOtp')->willReturn($otp);

        $this->otpRepo->expects($this->once())
            ->method('markAsUsed')
            ->with($otp);

        $this->otpRepo->expects($this->once())
            ->method('invalidateAllActiveOtps')
            ->with($userId, 'email');

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $result = $this->service->verifyOtp($userId, $channel, $inputOtp);

        $this->assertTrue($result);
    }

    public function test_fails_verification_when_otp_is_invalid()
    {
        $userId = 1;
        $channel = 'email';

        $otp = new OtpCode([
            'code_hash' => 'invalid_hash',
            'attempts' => 0,
        ]);

        $this->otpRepo->method('getValidOtp')->willReturn($otp);

        $this->otpRepo->expects($this->once())
            ->method('incrementAttempts')
            ->with($otp);

        $result = $this->service->verifyOtp($userId, $channel, 'wrong_otp');

        $this->assertFalse($result);
    }

    public function test_fails_when_max_attempts_reached()
    {
        $userId = 1;
        $channel = 'email';

        $otp = new OtpCode([
            'attempts' => 3,
        ]);

        $this->otpRepo->method('getValidOtp')->willReturn($otp);

        $this->otpRepo->expects($this->once())
            ->method('markAsUsed')
            ->with($otp);

        $result = $this->service->verifyOtp($userId, $channel, '123456');

        $this->assertFalse($result);
    }

    public function test_returns_false_when_no_valid_otp_found()
    {
        $this->otpRepo->method('getValidOtp')->willReturn(null);

        $result = $this->service->verifyOtp(1, 'email', '123456');

        $this->assertFalse($result);
    }
}
