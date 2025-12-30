<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'James',
            'email' => 'edinaldosantyago@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);
        User::factory()->create([
            'name' => 'Luisa',
            'email' => 'luisa@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);
    }
}
