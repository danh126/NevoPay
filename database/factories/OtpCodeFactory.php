<?php

namespace Database\Factories;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OtpCode>
 */
class OtpCodeFactory extends Factory
{
    protected $model = OtpCode::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'channel'    => 'email',
            'code_hash'  => hash('sha256', Str::random(6)),
            'expires_at' => now()->addMinutes(5),
            'attempts'   => 0,
            'is_used'    => false,
        ];
    }

    /**
     * OTP đã dùng
     */
    public function used(): self
    {
        return $this->state(fn () => [
            'is_used' => true,
        ]);
    }

    /**
     * OTP đã hết hạn
     */
    public function expired(): self
    {
        return $this->state(fn () => [
            'expires_at' => now()->subMinute(),
        ]);
    }

    /**
     * OTP vẫn còn hợp lệ
     */
    public function valid(): self
    {
        return $this->state(fn () => [
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * OTP của một user/channel khác
     */
    public function forUser(int $userId): self
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function forChannel(string $channel): self
    {
        return $this->state(fn () => [
            'channel' => $channel,
        ]);
    }
}
