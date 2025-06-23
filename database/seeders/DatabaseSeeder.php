<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed roles from RoleEnum
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate([
                'name' => $roleEnum->value,
            ]);
        }

        // 2. Create an admin user and assign ADMIN role
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        // 3. Create 100 random users
        $users = User::factory()->count(100)->create();

        // 4. Assign random roles to each user
        $roleNames = collect(RoleEnum::cases())->map(fn($role) => $role->value);

        foreach ($users as $user) {
            $randomRole = $roleNames->random();
            $user->assignRole($randomRole);
        }
    }
}
