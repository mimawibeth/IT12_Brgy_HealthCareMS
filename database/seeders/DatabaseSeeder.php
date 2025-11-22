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
                'email' => 'superadmin@bhc.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'super_admin',
            ]
        );

        // Admin Account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@bhc.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Worker (BHW) Account
        User::updateOrCreate(
            ['username' => 'worker'],
            [
                'name' => 'Barangay Health Worker',
                'email' => 'worker@bhc.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
            ]
        );
    }
}
