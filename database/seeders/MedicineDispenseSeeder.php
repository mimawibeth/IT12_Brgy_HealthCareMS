<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineDispense;
use Illuminate\Database\Seeder;

class MedicineDispenseSeeder extends Seeder
{
    private array $recipients = [
        'Walk-in patient', 'Clinic visitor', 'Immunization program', 'Antenatal clinic',
        'Family planning client', 'Chronic disease patient', 'Emergency referral', 'School health program',
    ];

    public function run(): void
    {
        $medicineIds = Medicine::pluck('id')->all();
        if (empty($medicineIds)) {
            return;
        }

        for ($i = 0; $i < 150; $i++) {
            $medicineId = fake()->randomElement($medicineIds);
            $quantity = fake()->numberBetween(1, 50);

            MedicineDispense::create([
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
                'dispensed_to' => fake()->randomElement($this->recipients),
                'reference_no' => fake()->optional(0.7)->bothify('REF-####-????'),
                'dispensed_at' => fake()->dateTimeBetween('-18 months', 'now')->format('Y-m-d'),
                'remarks' => fake()->optional(0.4)->randomElement([
                    'For 10-day course of treatment',
                    'Maintenance medication',
                    'Emergency supply',
                    'Regular stock dispensing',
                    'Medication for chronic condition management',
                ]),
            ]);
        }
    }
}
