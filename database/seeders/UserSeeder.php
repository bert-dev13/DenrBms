<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\UserAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Two administrator accounts; shared simple password for local/demo use.
     */
    public function run(): void
    {
        $password = Hash::make('admin123');

        $admins = [
            ['email' => 'admin1@denr.gov.ph', 'name' => 'Administrator One', 'username' => 'admin1'],
            ['email' => 'admin2@denr.gov.ph', 'name' => 'Administrator Two', 'username' => 'admin2'],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'username' => $admin['username'],
                    'password' => $password,
                    'role' => UserAccess::ROLE_ADMIN,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Administrator accounts created/updated successfully.');
        foreach ($admins as $admin) {
            $this->command->info("  • {$admin['email']} / admin123 — {$admin['name']}");
        }
    }
}
