<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'aleksandrmajlo@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin12345!'),
                'email_verified_at' => now(),
            ]
        );
    }
}
