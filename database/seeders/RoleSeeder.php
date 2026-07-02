<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator who manages riders, customers, orders, prices, commissions, and reports.',
            ],
            [
                'name' => 'rider',
                'description' => 'Delivery rider who receives orders, picks up packages, and delivers them to customers.',
            ],
            [
                'name' => 'customer',
                'description' => 'Customer who creates delivery orders and tracks riders.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}