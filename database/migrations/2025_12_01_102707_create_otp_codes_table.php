<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // liên kết user
            $table->string('channel', 20); // email, sms,... có thể mở rộng
            $table->string('code_hash'); // lưu hash của OTP, không lưu raw
            $table->timestamp('expires_at'); // thời gian hết hạn
            $table->unsignedInteger('attempts')->default(0); // số lần thử
            $table->boolean('is_used')->default(false); // đã dùng chưa
            $table->timestamps();

            $table->index(['user_id', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
