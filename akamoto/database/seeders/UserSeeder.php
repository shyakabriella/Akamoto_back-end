<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', User::ROLE_ADMIN)->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run RoleSeeder first.');
            return;
        }

        User::updateOrCreate(
            [
                'email' => 'admin@akamoto.com',
            ],
            [
                'role_id' => $adminRole->id,
                'name' => 'Akamoto Admin',
                'username' => 'akamoto_admin',
                'phone' => '250780000000',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $this->command->info('Admin user created successfully.');
    }
}