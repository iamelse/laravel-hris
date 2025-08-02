<?php

namespace Database\Seeders;

use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReimbursementSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $titles = [
            'Taxi to client meeting',
            'Hotel stay for conference',
            'Office lunch with team',
            'Printer ink purchase',
            'Flight to Jakarta',
            'Training seminar fee',
            'Grab ride to HQ',
        ];

        foreach (range(1, 50) as $i) {
            Reimbursement::create([
                'user_id'     => fake()->randomElement($users),
                'title'       => fake()->randomElement($titles),
                'amount'      => fake()->randomFloat(2, 10000, 2000000), // Rp10.000 – Rp2.000.000
                'description' => fake()->optional()->sentence(8),
                'proof_file'  => null, // bisa nanti diisi saat upload
                'status'      => fake()->randomElement(['pending', 'approved', 'rejected']),
                'created_at'  => now()->subDays(rand(1, 90)),
                'updated_at'  => now(),
            ]);
        }
    }
}