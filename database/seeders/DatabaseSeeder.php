<?php

use App\Enums\RoleEnum;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\LeaveApplication;
use Database\Seeders\ReimbursementSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed roles from RoleEnum
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate([
                'name' => $roleEnum->value,
                'guard_name' => 'web',
            ]);
        }

        // 2. Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        // 3. Create a specific employee user
        $employee = User::factory()->create([
            'name' => 'Employee User',
            'username' => 'employee',
            'email' => 'employee@example.com',
            'email_verified_at' => now(),
        ]);
        $employee->assignRole(RoleEnum::EMPLOYEE->value);

        // 4. Create 100 random users and assign random roles
        $users = User::factory()->count(100)->create();
        $roleNames = collect(RoleEnum::cases())->map(fn($role) => $role->value);

        foreach ($users as $user) {
            $user->assignRole($roleNames->random());
        }

        // 5. Create dummy leave applications
        LeaveApplication::factory(100)->create();
        $this->call(ReimbursementSeeder::class);
    }
}