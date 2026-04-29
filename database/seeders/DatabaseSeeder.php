<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@konnco.com',
            'role' => 'super_admin',
        ]);

        User::factory()->create([
            'name' => 'Finance',
            'email' => 'finance@konnco.com',
            'role' => 'finance',
        ]);

        User::factory()->create([
            'name' => 'Finance Manager',
            'email' => 'financemanager@konnco.com',
            'role' => 'finance_manager',
        ]);

        User::factory()->create([
            'name' => 'HRGA',
            'email' => 'hrga@konnco.com',
            'role' => 'hrga',
        ]);

        User::factory()->create([
            'name' => 'accounting',
            'email' => 'accounting@konnco.com',
            'role' => 'accounting',
        ]);

    }
}