<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineDispense;
use Illuminate\Database\Seeder;

class MedicineDispenseSeeder extends Seeder
{
    public function run(): void
    {
        $medicineIds = Medicine::pluck('id')->all();
        if (empty($medicineIds)) {
            return;
        }

        for ($i = 0; $i < 100; $i++) {
            $medicineId = fake()->randomElement($medicineIds);

            MedicineDispense::create([
                'medicine_id' => $medicineId,
                'quantity' => fake()->numberBetween(1, 30),
                'dispensed_to' => fake()->name(),
                'reference_no' => fake()->optional()->bothify('REF-####-????'),
                'dispensed_at' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                'remarks' => fake()->optional()->sentence(),
            ]);
        }
    }
}
