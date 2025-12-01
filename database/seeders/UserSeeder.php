<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'full_name' => 'Admin',
            'email' => 'admin@nevo.com',
            'phone_number' => fake()->unique()->phoneNumber(),
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => true,
            'two_factor_enabled' => false,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Users bình thường
        User::factory()->count(10)->create();
    }
}
