<?php

namespace Database\Seeders;

use App\Models\NipRecord;
use App\Models\NipVisit;
use Illuminate\Database\Seeder;

class NipSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $dob = fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d');
            $date = fake()->dateTimeBetween($dob, 'now')->format('Y-m-d');

            $record = NipRecord::create([
                'record_no' => 'NIP-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'date' => $date,
                'child_name' => fake()->name(),
                'dob' => $dob,
                'address' => fake()->address(),
                'mother_name' => fake()->name('female'),
                'father_name' => fake()->optional()->name('male'),
                'contact' => fake()->phoneNumber(),
                'place_delivery' => fake()->randomElement(['Hospital', 'Clinic', 'Home', 'Lying-in']),
                'attended_by' => fake()->randomElement(['Doctor', 'Midwife', 'Nurse', 'Hilot']),
                'sex_baby' => fake()->randomElement(['M', 'F']),
                'nhts_4ps_id' => fake()->optional()->numerify('4PS-########'),
                'phic_id' => fake()->optional()->numerify('PHIC-##########'),
                'tt_status_mother' => fake()->randomElement(['Complete', 'Incomplete', 'Unknown']),
                'birth_length' => fake()->optional()->randomFloat(1, 45, 60),
                'birth_weight' => fake()->randomFloat(2, 2.0, 4.5),
                'delivery_type' => fake()->randomElement(['NSD', 'CS', 'Assisted']),
                'initiated_breastfeeding' => fake()->randomElement(['yes', 'no']),
                'birth_order' => fake()->optional()->numberBetween(1, 8),
                'newborn_screening_date' => fake()->dateTimeBetween($dob, '+1 months')->format('Y-m-d'),
                'newborn_screening_result' => fake()->randomElement(['Normal', 'For follow-up']),
                'hearing_test_screened' => fake()->randomElement(['pass', 'fail']),
                'vit_k' => fake()->randomElement(['given', 'not_given']),
                'bcg' => fake()->randomElement(['given', 'not_given']),
                'hepa_b_24h' => fake()->randomElement(['yes', 'no']),
            ]);

            // 1-3 visits per child
            $visitCount = fake()->numberBetween(1, 3);
            for ($v = 0; $v < $visitCount; $v++) {
                NipVisit::create([
                    'nip_record_id' => $record->id,
                    'visit_date' => fake()->dateTimeBetween($dob, 'now')->format('Y-m-d'),
                    'age_months' => fake()->numberBetween(0, 12),
                    'weight' => fake()->randomFloat(2, 2.0, 12.0),
                    'length' => fake()->randomFloat(1, 45, 80),
                    'status' => fake()->optional()->randomElement(['Normal', 'Underweight', 'Stunted']),
                    'breastfeeding' => fake()->randomElement(['yes', 'no']),
                    'temperature' => fake()->randomFloat(1, 36, 38),
                    'vaccine' => fake()->randomElement(['BCG', 'Penta', 'OPV', 'PCV', 'MMR', 'Hepa B']),
                ]);
            }
        }
    }
}
