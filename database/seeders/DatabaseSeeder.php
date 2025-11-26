<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createDefaultUsers();
    }

    /**
     * Seed default admin and employee accounts.
     */
    protected function createDefaultUsers(): void
    {
        // Super Admin Account
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'first_name' => 'Super',
                'middle_name' => null,
                'last_name' => 'Administrator',
                'email' => 'superadmin@bhc.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'super_admin',
                'contact_number' => '09170000001',
                'status' => 'active',
            ]
        );

        // Admin Account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'User',
                'email' => 'admin@bhc.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'contact_number' => '09170000002',
                'status' => 'active',
            ]
        );

        // Worker (BHW) Account
        User::updateOrCreate(
            ['username' => 'worker'],
            [
                'name' => 'Barangay Health Worker',
                'first_name' => 'Barangay',
                'middle_name' => 'Health',
                'last_name' => 'Worker',
                'email' => 'worker@bhc.com',
                'password' => Hash::make('worker123'),
                'role' => 'bhw',
                'contact_number' => '09170000003',
                'status' => 'active',
            ]
        );
    }
}
