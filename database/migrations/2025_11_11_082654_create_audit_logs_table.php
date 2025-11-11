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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Ai thực hiện hành động
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Loại hành động
            $table->enum('action', ['create', 'update', 'delete', 'login', 'logout', 'system']);

            // Bảng và bản ghi nào bị ảnh hưởng
            $table->string('auditable_type'); // App\Models\Transaction, App\Models\Wallet, ...
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Dữ liệu cũ & mới (JSON)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Mô tả chi tiết
            $table->text('description')->nullable();

            $table->timestamps();

            // Index để tối ưu truy vấn
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
