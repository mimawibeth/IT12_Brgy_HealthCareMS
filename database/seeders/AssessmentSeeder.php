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

        for ($i = 0; $i < 150; $i++) {
            $patientId = fake()->randomElement($patientIds);
            $date = fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d');
            $age = (int) fake()->numberBetween(20, 85);

            // More realistic CVD risk assessment
            $sbp = (int) fake()->numberBetween(90, 180);
            $dbp = (int) fake()->numberBetween(60, 120);
            $cvdRisk = $this->calculateCVDRisk($sbp, $dbp, $age);

            $wt = fake()->randomFloat(1, 40, 120);
            $ht = fake()->randomFloat(1, 140, 190);
            $bmi = round($wt / pow($ht / 100, 2), 2);

            $fbs = fake()->optional(0.7)->randomFloat(1, 70, 250);
            $lipidProfile = $this->assessLipidProfile($fbs, $wt, $ht);

            Assessment::create([
                'PatientID' => $patientId,
                'date' => $date,
                'age' => (string) $age,
                'cvdRisk' => $cvdRisk,
                'bpSystolic' => (string) $sbp,
                'bpDiastolic' => (string) $dbp,
                'wt' => (string) $wt,
                'ht' => (string) $ht,
                'fbsRbs' => $fbs,
                'lipidProfile' => $lipidProfile,
                'urineKetones' => fake()->optional(0.8)->randomElement(['Negative', 'Trace', '+1', '+2']),
                'urineProtein' => fake()->optional(0.8)->randomElement(['Negative', 'Trace', '+1', '+2']),
                'footCheck' => fake()->optional(0.7)->randomElement(['Normal', 'Callus', 'Ulcer', 'Amputation']),
                'chiefComplaint' => fake()->optional(0.6)->randomElement([
                    'Hypertension',
                    'Chest pain',
                    'Shortness of breath',
                    'Dizziness',
                    'Fatigue',
                    'Joint pain',
                    'Headache',
                    'No complaints',
                ]),
                'historyPhysical' => fake()->optional(0.5)->randomElement([
                    'Patient reports frequent headaches and elevated BP readings at home.',
                    'Physical exam reveals overweight status. Advised lifestyle modification.',
                    'Patient compliant with medications. BP readings stable.',
                    'No significant findings on physical examination.',
                    'Patient has history of diabetes. Blood glucose management ongoing.',
                ]),
                'management' => fake()->optional(0.6)->randomElement([
                    'Continue current antihypertensive therapy. Follow-up in 2 weeks.',
                    'Prescribed lifestyle modifications: diet, exercise, weight reduction.',
                    'Referral to cardiologist for further evaluation.',
                    'Increase medication dosage. Follow-up after 1 month.',
                    'Health education provided. Schedule next visit in 3 months.',
                ]),
            ]);
        }
    }

    private function calculateCVDRisk(int $sbp, int $dbp, int $age): string
    {
        if ($sbp >= 160 || $dbp >= 100 || $age >= 70) {
            return 'Very High';
        } elseif ($sbp >= 140 || $dbp >= 90 || $age >= 60) {
            return 'High';
        } elseif ($sbp >= 130 || $dbp >= 85 || $age >= 45) {
            return 'Moderate';
        } else {
            return 'Low';
        }
    }

    private function assessLipidProfile(?float $fbs, float $wt, float $ht): string
    {
        $bmi = $wt / pow($ht / 100, 2);

        if ($fbs && $fbs > 200) {
            return 'High Cholesterol';
        } elseif ($bmi > 30 || ($fbs && $fbs > 140)) {
            return 'Borderline';
        } else {
            return 'Normal';
        }
    }
}
