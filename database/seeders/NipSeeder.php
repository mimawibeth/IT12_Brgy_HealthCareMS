<?php

namespace Database\Seeders;

use App\Models\NipRecord;
use App\Models\NipVisit;
use Illuminate\Database\Seeder;

class NipSeeder extends Seeder
{
    private array $vaccines = [
        'BCG', 'Hepatitis B (HepB)', 'Pneumococcal Conjugate (PCV)',
        'Pentavalent (Penta)', 'Oral Polio Vaccine (OPV)', 'Inactivated Polio Vaccine (IPV)',
        'Measles/MMR', 'Japanese Encephalitis (JE)', 'Varicella',
    ];

    private array $filipinoFirstNames = [
        'Maria', 'Juan', 'Rosa', 'Jose', 'Ana', 'Manuel', 'Carmen', 'Luis', 'Sofia', 'Miguel',
        'James', 'Christian', 'Paul', 'Mark', 'John', 'Angel', 'Princess', 'Jade', 'Faith', 'Grace',
    ];

    private array $filipinoLastNames = [
        'Santos', 'Dela Cruz', 'Garcia', 'Rivera', 'Lopez', 'Martinez', 'Gonzales', 'Reyes', 'Torres', 'Flores',
    ];

    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $dob = fake()->dateTimeBetween('-24 months', 'now')->format('Y-m-d');
            $date = fake()->dateTimeBetween($dob, 'now')->format('Y-m-d');
            $childFirstName = fake()->randomElement($this->filipinoFirstNames);
            $lastName = fake()->randomElement($this->filipinoLastNames);
            $motherFirstName = fake()->randomElement(['Maria', 'Rosa', 'Ana', 'Carmen', 'Sofia']);

            $record = NipRecord::create([
                'record_no' => 'NIP-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'date' => $date,
                'child_name' => $childFirstName . ' ' . $lastName,
                'dob' => $dob,
                'address' => fake()->numberBetween(1, 999) . ' ' . fake()->streetName() . ', Barangay ' . fake()->numberBetween(1, 32),
                'mother_name' => $motherFirstName . ' ' . $lastName,
                'father_name' => fake()->optional(0.75)->randomElement(['Juan', 'Jose', 'Carlos', 'Luis', 'Pedro']) . ' ' . $lastName,
                'contact' => '09' . fake()->numberBetween(100000000, 999999999),
                'place_delivery' => fake()->randomElement(['Hospital', 'Clinic', 'Home', 'Lying-in']),
                'attended_by' => fake()->randomElement(['Doctor', 'Midwife', 'Nurse', 'Hilot']),
                'sex_baby' => fake()->randomElement(['M', 'F']),
                'nhts_4ps_id' => fake()->optional(0.3)->numerify('4PS-########'),
                'phic_id' => fake()->optional(0.8)->numerify('PHIC-##########'),
                'tt_status_mother' => fake()->randomElement(['Complete', 'Incomplete', 'Unknown']),
                'birth_length' => fake()->optional(0.85)->randomFloat(1, 48, 56),
                'birth_weight' => fake()->randomFloat(2, 2.5, 4.0),
                'delivery_type' => fake()->randomElement(['NSD', 'CS', 'Assisted']),
                'initiated_breastfeeding' => fake()->randomElement(['yes', 'no']),
                'birth_order' => fake()->optional(0.9)->numberBetween(1, 8),
                'newborn_screening_date' => fake()->dateTimeBetween($dob, '+1 months')->format('Y-m-d'),
                'newborn_screening_result' => fake()->randomElement(['Normal', 'For follow-up']),
                'hearing_test_screened' => fake()->randomElement(['pass', 'fail']),
                'vit_k' => fake()->randomElement(['given', 'not_given']),
                'bcg' => fake()->randomElement(['given', 'not_given']),
                'hepa_b_24h' => fake()->randomElement(['yes', 'no']),
            ]);

            // 2-5 visits per child (more realistic immunization schedule)
            $visitCount = fake()->numberBetween(2, 5);
            $ageMonths = 0;
            for ($v = 0; $v < $visitCount; $v++) {
                $scheduleAges = [0, 1, 2, 3, 6, 9, 12, 18]; // Common immunization schedule ages in months
                $ageMonths = $scheduleAges[$v] ?? fake()->numberBetween(0, 24);

                NipVisit::create([
                    'nip_record_id' => $record->id,
                    'visit_date' => fake()->dateTimeBetween($dob, 'now')->format('Y-m-d'),
                    'age_months' => min($ageMonths, 24),
                    'weight' => fake()->randomFloat(2, 2.5, 15.0),
                    'length' => fake()->randomFloat(1, 48, 90),
                    'status' => fake()->optional(0.85)->randomElement(['Normal', 'Underweight', 'Stunted', 'Wasted']),
                    'breastfeeding' => $v < 2 ? 'yes' : fake()->randomElement(['yes', 'no']),
                    'temperature' => fake()->randomFloat(1, 36.5, 37.5),
                    'vaccine' => fake()->randomElement($this->vaccines),
                ]);
            }
        }
    }
}
