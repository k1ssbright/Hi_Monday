<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed default system users.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'prefix' => 'นาย',
                'firstname' => 'ทดสอบ',
                'surname' => 'ระบบ',
                'password' => Hash::make('password'),
                'active_status' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'prefix' => 'Mr.',
                'firstname' => 'Admin',
                'surname' => 'User',
                'password' => Hash::make('password'),
                'active_status' => 1,
            ]
        );
    }
}
