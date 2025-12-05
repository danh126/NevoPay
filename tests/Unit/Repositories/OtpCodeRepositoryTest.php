<?php

namespace Tests\Unit\Repositories;

use App\Models\OtpCode;
use App\Models\User;
use App\Repositories\OtpCodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpCodeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected OtpCodeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OtpCodeRepository();
    }

    public function test_creates_otp_and_disables_old_active_otps()
    {
        $user = User::factory()->create();
        $channel = 'email';

        // Tạo OTP cũ còn hạn
        $oldOtp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Tạo OTP mới
        $newOtp = $this->repository->createOtp(
            $user->id,
            $channel,
            'hashed_code',
            now()->addMinutes(10)
        );

        // OTP mới được tạo
        $this->assertDatabaseHas('otp_codes', [
            'id'        => $newOtp->id,
            'is_used'   => false,
        ]);

        // OTP cũ phải bị vô hiệu hoá
        $this->assertTrue($oldOtp->fresh()->is_used);
    }

    public function test_returns_only_valid_otp()
    {
        $user = User::factory()->create();
        $channel = 'email';

        // OTP hết hạn
        OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->subMinute(),
        ]);

        // OTP hợp lệ
        $validOtp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->addMinute(),
        ]);

        $result = $this->repository->getValidOtp($user->id, $channel);

        $this->assertNotNull($result);
        $this->assertEquals($validOtp->id, $result->id);
    }

    public function test_increments_attempts_correctly()
    {
        $user = User::factory()->create();
        $otp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'attempts' => 0
        ]);

        $updated = $this->repository->incrementAttempts($otp);

        $this->assertEquals(1, $updated->attempts);
    }

    public function test_marks_otp_as_used()
    {
        $user = User::factory()->create();
        $otp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'is_used' => false
        ]);

        $updated = $this->repository->markAsUsed($otp);

        $this->assertTrue($updated->is_used);
    }

    public function test_invalidates_all_active_otps()
    {
        $user = User::factory()->create();
        $channel = 'email';

        $active1 = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        $active2 = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        // OTP đã dùng hoặc hết hạn → không bị update
        $inactive = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => true,
            'expires_at' => now()->addMinutes(10),
        ]);

        $expired = OtpCode::factory()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'is_used' => false,
            'expires_at' => now()->subMinute(),
        ]);

        $this->repository->invalidateAllActiveOtps($user->id, $channel);

        $this->assertTrue($active1->fresh()->is_used);
        $this->assertTrue($active2->fresh()->is_used);

        // Không bị ảnh hưởng
        $this->assertTrue($inactive->fresh()->is_used);
        $this->assertFalse($expired->fresh()->is_used);
    }
}
