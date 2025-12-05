<?php

namespace Database\Seeders;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class OtpCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!app()->environment('local')){
            $this->command->warn('OtpCode seeder is only for local environment. Skipping...');
            return;
        }

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            OtpCode::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
