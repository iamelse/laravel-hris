<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveApplication>
 */
class LeaveApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+'.rand(1, 5).' days');

        // ✅ Dapatkan user yang punya role admin
        $adminUser = User::whereHas('roles', function ($query) {
                        $query->where('name', RoleEnum::ADMIN->value); // ✅ gunakan ->value
                    })->inRandomOrder()->first();

        return [
            'user_id'     => User::inRandomOrder()->first()?->id ?? User::factory(),
            'start_date'  => $startDate->format('Y-m-d'),
            'end_date'    => $endDate->format('Y-m-d'),
            'reason'      => $this->faker->sentence(),
            'status'      => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'approved_by' => $this->faker->boolean(70) && $adminUser ? $adminUser->id : null,
        ];
    }
}
