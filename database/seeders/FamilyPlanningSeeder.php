<?php

namespace Database\Seeders;

use App\Models\FamilyPlanningRecord;
use Illuminate\Database\Seeder;

class FamilyPlanningSeeder extends Seeder
{
    private array $filipinoNames = [
        'Maria', 'Rosa', 'Ana', 'Carmen', 'Sofia', 'Elena', 'Lucia', 'Gloria', 'Theresa', 'Josephine',
        'Juan', 'Jose', 'Carlos', 'Luis', 'Pedro', 'Miguel', 'Ramon', 'Antonio', 'Francisco', 'Manuel',
    ];

    private array $filipinoLastNames = [
        'Santos', 'Dela Cruz', 'Garcia', 'Rivera', 'Lopez', 'Martinez', 'Gonzales', 'Reyes', 'Torres', 'Flores',
    ];

    private array $fpMethods = [
        'Oral Contraceptive Pills (OCP)',
        'Intrauterine Device (IUD)',
        'Injectable Contraceptive (Depo-Provera)',
        'Implant (Norplant)',
        'Barrier Methods (Condom)',
        'Tubectomy',
        'Vasectomy',
    ];

    public function run(): void
    {
        for ($i = 0; $i < 80; $i++) {
            $lastDelivery = fake()->optional(0.65)->dateTimeBetween('-8 years', '-1 months');
            $lastPeriod = fake()->optional(0.85)->dateTimeBetween('-2 months', 'now');
            $consentDate = fake()->optional(0.8)->dateTimeBetween('-2 years', 'now');
            $firstName = fake()->randomElement($this->filipinoNames);
            $lastName = fake()->randomElement($this->filipinoLastNames);
            $spouseName = fake()->randomElement($this->filipinoNames) . ' ' . fake()->randomElement($this->filipinoLastNames);
            $clientType = fake()->randomElement([
                'New Acceptor',
                'Current User',
                'Changing Method',
                'Postpartum',
            ]);

            FamilyPlanningRecord::create([
                'record_no' => 'FP-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'client_name' => $firstName . ' ' . $lastName,
                'dob' => fake()->dateTimeBetween('-49 years', '-18 years')->format('Y-m-d'),
                'age' => fake()->numberBetween(18, 49),
                'address' => fake()->numberBetween(1, 999) . ' ' . fake()->streetName() . ', Barangay ' . fake()->numberBetween(1, 32),
                'contact' => '09' . fake()->numberBetween(100000000, 999999999),
                'occupation' => fake()->optional(0.7)->randomElement(['Housewife', 'Farmer', 'Vendor', 'Laborer', 'Retail Staff']),
                'spouse_name' => fake()->optional(0.85)->randomElement($this->filipinoNames) . ' ' . $lastName,
                'spouse_age' => fake()->optional(0.85)->numberBetween(18, 65),
                'children_count' => fake()->optional(0.9)->numberBetween(0, 8),
                'client_type' => $clientType,
                'reason' => [
                    fake()->randomElement([
                        'Spacing births',
                        'Limiting births',
                        'Economic reasons',
                        'Health reasons',
                        'Personal choice',
                    ])
                ],
                'medical_history' => [
                    fake()->optional(0.4)->randomElement(['Hypertension', 'Diabetes', 'Migraine', 'None']),
                    fake()->optional(0.3)->randomElement(['Allergy', 'Heart disease', 'Asthma', 'None']),
                ],
                'gravida' => fake()->optional(0.9)->numberBetween(1, 8),
                'para' => fake()->optional(0.85)->numberBetween(0, 7),
                'last_delivery' => $lastDelivery ? $lastDelivery->format('Y-m-d') : null,
                'last_period' => $lastPeriod ? $lastPeriod->format('Y-m-d') : null,
                'menstrual_flow' => fake()->optional(0.8)->randomElement(['Light', 'Moderate', 'Heavy']),
                'dysmenorrhea' => fake()->optional(0.7)->randomElement(['Yes', 'No']),
                'sti_risk' => [fake()->optional(0.05)->randomElement(['Yes', 'No'])],
                'vaw_risk' => [fake()->optional(0.08)->randomElement(['Yes', 'No'])],
                'bp' => fake()->optional(0.9)->numerify('1##/##'),
                'weight' => fake()->optional(0.9)->randomFloat(1, 45, 95),
                'height' => fake()->optional(0.85)->randomFloat(1, 140, 180),
                'exam_findings' => fake()->optional(0.6)->randomElement([
                    'Normal physical examination',
                    'Mild obesity noted',
                    'Varicose veins observed',
                    'No abnormal findings',
                ]),
                'counseled_by' => fake()->optional(0.8)->randomElement(['BHW', 'Nurse', 'Midwife', 'Doctor']),
                'client_signature' => fake()->optional(0.75)->randomElement($this->filipinoNames),
                'consent_date' => $consentDate ? $consentDate->format('Y-m-d') : null,
            ]);
        }
    }
}
