<?php

namespace Database\Seeders;

use App\Models\FamilyPlanningRecord;
use Illuminate\Database\Seeder;

class FamilyPlanningSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $lastDelivery = fake()->optional()->dateTimeBetween('-10 years', 'now');
            $lastPeriod = fake()->optional()->dateTimeBetween('-3 months', 'now');
            $consentDate = fake()->optional()->dateTimeBetween('-1 years', 'now');
            FamilyPlanningRecord::create([
                'record_no' => 'FP-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'client_name' => fake()->name(),
                'dob' => fake()->dateTimeBetween('-49 years', '-18 years')->format('Y-m-d'),
                'age' => fake()->numberBetween(18, 49),
                'address' => fake()->address(),
                'contact' => fake()->phoneNumber(),
                'occupation' => fake()->optional()->jobTitle(),
                'spouse_name' => fake()->optional()->name(),
                'spouse_age' => fake()->optional()->numberBetween(18, 60),
                'children_count' => fake()->optional()->numberBetween(0, 10),
                'client_type' => fake()->optional()->randomElement(['New Acceptor', 'Current User', 'Changing Method', 'Postpartum']),
                'reason' => [fake()->sentence()],
                'medical_history' => [fake()->word(), fake()->word()],
                'gravida' => fake()->optional()->numberBetween(0, 6),
                'para' => fake()->optional()->numberBetween(0, 6),
                'last_delivery' => $lastDelivery ? $lastDelivery->format('Y-m-d') : null,
                'last_period' => $lastPeriod ? $lastPeriod->format('Y-m-d') : null,
                'menstrual_flow' => fake()->optional()->randomElement(['Light', 'Moderate', 'Heavy']),
                'dysmenorrhea' => fake()->optional()->randomElement(['Yes', 'No']),
                'sti_risk' => [fake()->optional()->randomElement(['Yes', 'No'])],
                'vaw_risk' => [fake()->optional()->randomElement(['Yes', 'No'])],
                'bp' => fake()->optional()->numerify('1##/##'),
                'weight' => fake()->optional()->randomFloat(1, 40, 100),
                'height' => fake()->optional()->randomFloat(1, 140, 190),
                'exam_findings' => fake()->optional()->sentence(),
                'counseled_by' => fake()->optional()->name(),
                'client_signature' => fake()->optional()->name(),
                'consent_date' => $consentDate ? $consentDate->format('Y-m-d') : null,
            ]);
        }
    }
}
