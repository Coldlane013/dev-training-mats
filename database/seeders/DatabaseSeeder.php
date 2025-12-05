<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bank;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'email_verified_at' => now(),
            'first_name' => 'Admin',
            'middle_name' => '',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
            'user_roles' => 'admin',
        ]);

        User::factory(100)->create();
        
        Bank::factory(100)->create();
    }
}
