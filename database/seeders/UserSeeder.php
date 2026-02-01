<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a regular user
        User::updateOrCreate(
            ['email' => 'test@denr.gov.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create or update an admin user
        User::updateOrCreate(
            ['email' => 'admin@denr.gov.ph'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users created/updated successfully!');
        $this->command->info('Regular User: test@denr.gov.ph / password123');
        $this->command->info('Admin User: admin@denr.gov.ph / admin123');
    }
}
