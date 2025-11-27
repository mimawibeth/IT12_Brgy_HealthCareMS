<?php

namespace Database\Seeders;

use App\Models\PrenatalRecord;
use App\Models\PrenatalVisit;
use Illuminate\Database\Seeder;

class PrenatalSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $lastDelivery = fake()->optional()->dateTimeBetween('-10 years', 'now');
            $td1 = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $td2 = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $td3 = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $td4 = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $td5 = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $tdl = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $record = PrenatalRecord::create([
                'record_no' => 'PRE-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'mother_name' => fake()->name('female'),
                'purok' => 'Purok ' . fake()->numberBetween(1, 7),
                'age' => fake()->numberBetween(18, 45),
                'dob' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                'occupation' => fake()->optional()->jobTitle(),
                'education' => fake()->optional()->randomElement(['Elementary', 'High School', 'College', 'Post-grad']),
                'is_4ps' => fake()->boolean(30),
                'four_ps_no' => fake()->optional(0.3)->numerify('4PS-########'),
                'cell' => fake()->phoneNumber(),
                'lmp' => fake()->dateTimeBetween('-9 months', '-1 months')->format('Y-m-d'),
                'edc' => fake()->dateTimeBetween('+1 months', '+3 months')->format('Y-m-d'),
                'urinalysis' => fake()->optional()->randomElement(['Normal', 'Proteinuria', 'Glycosuria']),
                'gravida' => fake()->numberBetween(1, 6),
                'para' => fake()->numberBetween(0, 5),
                'abortion' => fake()->numberBetween(0, 2),
                'delivery_count' => fake()->numberBetween(0, 5),
                'last_delivery_date' => $lastDelivery ? $lastDelivery->format('Y-m-d') : null,
                'delivery_type' => fake()->optional()->randomElement(['NSD', 'CS', 'Assisted']),
                'hemoglobin_first' => fake()->optional()->randomFloat(1, 8, 14),
                'hemoglobin_second' => fake()->optional()->randomFloat(1, 8, 14),
                'blood_type' => fake()->optional()->randomElement(['A', 'B', 'AB', 'O']) . fake()->optional()->randomElement(['+', '-']),
                'urinalysis_protein' => fake()->optional()->randomElement(['Negative', 'Trace', '+1', '+2']),
                'urinalysis_sugar' => fake()->optional()->randomElement(['Negative', 'Trace', '+1', '+2']),
                'husband_name' => fake()->optional()->name('male'),
                'husband_occupation' => fake()->optional()->jobTitle(),
                'husband_education' => fake()->optional()->randomElement(['Elementary', 'High School', 'College']),
                'family_religion' => fake()->optional()->randomElement(['Roman Catholic', 'INC', 'Born Again', 'Others']),
                'amount_prepared' => fake()->optional()->randomElement(['< 5,000', '5,000 - 10,000', '> 10,000']),
                'philhealth_member' => fake()->optional()->randomElement(['Yes', 'No']),
                'delivery_location' => fake()->optional()->randomElement(['BHS', 'RHUs', 'Clinic', 'Home']),
                'delivery_partner' => fake()->optional()->randomElement(['Midwife', 'Doctor', 'Hilot']),
                'td1' => $td1 ? $td1->format('Y-m-d') : null,
                'td2' => $td2 ? $td2->format('Y-m-d') : null,
                'td3' => $td3 ? $td3->format('Y-m-d') : null,
                'td4' => $td4 ? $td4->format('Y-m-d') : null,
                'td5' => $td5 ? $td5->format('Y-m-d') : null,
                'tdl' => $tdl ? $tdl->format('Y-m-d') : null,
                'fbs' => fake()->optional()->randomFloat(1, 70, 140),
                'rbs' => fake()->optional()->randomFloat(1, 70, 200),
                'ogtt' => fake()->optional()->randomFloat(1, 70, 200),
                'vdrl' => fake()->optional()->randomElement(['Reactive', 'Non-reactive']),
                'hbsag' => fake()->optional()->randomElement(['Reactive', 'Non-reactive']),
                'hiv' => fake()->optional()->randomElement(['Reactive', 'Non-reactive']),
                'extra' => [
                    'notes' => fake()->optional()->sentence(),
                ],
            ]);

            // 1-3 visits per prenatal record
            $visitCount = fake()->numberBetween(1, 3);
            for ($v = 0; $v < $visitCount; $v++) {
                PrenatalVisit::create([
                    'prenatal_record_id' => $record->id,
                    'date' => fake()->dateTimeBetween('-8 months', 'now')->format('Y-m-d'),
                    'trimester' => fake()->optional()->randomElement(['1st', '2nd', '3rd']),
                    'risk' => fake()->optional()->randomElement(['Low', 'High']),
                    'first_visit' => $v === 0 ? 'Yes' : 'No',
                    'subjective' => fake()->optional()->sentence(),
                    'aog' => fake()->optional()->numberBetween(8, 40) . ' weeks',
                    'weight' => fake()->optional()->randomFloat(1, 40, 90),
                    'height' => fake()->optional()->randomFloat(1, 140, 180),
                    'bp' => fake()->optional()->numerify('1##/##'),
                    'pr' => fake()->optional()->numberBetween(60, 110),
                    'fh' => fake()->optional()->numerify('##'),
                    'fht' => fake()->optional()->numerify('1##'),
                    'presentation' => fake()->optional()->randomElement(['Cephalic', 'Breech', 'Transverse']),
                    'bmi' => fake()->optional()->randomFloat(1, 18, 35),
                    'rr' => fake()->optional()->numberBetween(16, 30),
                    'hr' => fake()->optional()->numberBetween(60, 120),
                    'assessment' => fake()->optional()->sentence(),
                    'plan' => fake()->optional()->sentence(),
                ]);
            }
        }
    }
}
