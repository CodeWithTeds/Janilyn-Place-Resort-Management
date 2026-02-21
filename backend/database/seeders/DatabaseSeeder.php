<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        // Create Owner
        User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'role' => UserRole::OWNER,
            'password' => 'password',
        ]);

        // Create Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
            'password' => 'password',
        ]);

        // Create Staff
        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => UserRole::STAFF,
            'password' => 'password',
        ]);
    }
}
