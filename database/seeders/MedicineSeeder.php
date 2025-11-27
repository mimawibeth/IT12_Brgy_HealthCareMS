<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Medicine::create([
                'name' => fake()->randomElement(['Paracetamol', 'Amoxicillin', 'Ibuprofen', 'Cotrimoxazole', 'Metformin']) . ' ' . fake()->word(),
                'generic_name' => fake()->randomElement(['Paracetamol', 'Amoxicillin', 'Ibuprofen', 'Cotrimoxazole', 'Metformin']),
                'dosage_form' => fake()->randomElement(['Tablet', 'Capsule', 'Syrup', 'Suspension', 'Injection']),
                'strength' => fake()->randomElement(['125 mg', '250 mg', '500 mg', '5 mg/5 mL', '10 mg/mL']),
                'unit' => fake()->randomElement(['tablet', 'capsule', 'mL', 'ampoule']),
                'quantity_on_hand' => fake()->numberBetween(0, 500),
                'reorder_level' => fake()->numberBetween(20, 100),
                'expiry_date' => fake()->dateTimeBetween('now', '+3 years')->format('Y-m-d'),
                'remarks' => fake()->optional()->sentence(),
            ]);
        }
    }
}
