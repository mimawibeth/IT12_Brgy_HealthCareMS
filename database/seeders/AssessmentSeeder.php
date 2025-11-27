<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $patientIds = Patient::pluck('PatientID')->all();
        if (empty($patientIds)) {
            return;
        }

        for ($i = 0; $i < 100; $i++) {
            $patientId = fake()->randomElement($patientIds);
            $date = fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d');

            Assessment::create([
                'PatientID' => $patientId,
                'date' => $date,
                'age' => (string) fake()->numberBetween(1, 90),
                'cvdRisk' => fake()->optional()->randomElement(['Low', 'Moderate', 'High', 'Very High']),
                'bpSystolic' => fake()->numerify('1##'),
                'bpDiastolic' => fake()->numerify('##'),
                'wt' => (string) fake()->randomFloat(1, 30, 120),
                'ht' => (string) fake()->randomFloat(1, 120, 190),
                'fbsRbs' => fake()->optional()->randomFloat(1, 70, 250),
                'lipidProfile' => fake()->optional()->randomElement(['Normal', 'Borderline', 'High Cholesterol']),
                'urineKetones' => fake()->optional()->randomElement(['Negative', 'Trace', '+1', '+2']),
                'urineProtein' => fake()->optional()->randomElement(['Negative', 'Trace', '+1', '+2']),
                'footCheck' => fake()->optional()->randomElement(['Normal', 'Callus', 'Ulcer', 'Amputation']),
                'chiefComplaint' => fake()->optional()->sentence(),
                'historyPhysical' => fake()->optional()->paragraph(),
                'management' => fake()->optional()->paragraph(),
            ]);
        }
    }
}
