<?php

namespace Database\Seeders;

use App\Models\PrenatalRecord;
use App\Models\PrenatalVisit;
use Illuminate\Database\Seeder;

class PrenatalSeeder extends Seeder
{
    private array $filipinoFirstNames = [
        'Maria', 'Rosa', 'Ana', 'Carmen', 'Sofia', 'Elena', 'Lucia', 'Gloria', 'Theresa', 'Josephine',
        'Angela', 'Victoria', 'Catalina', 'Esperanza', 'Magdalena', 'Josefina', 'Cristina', 'Isabel', 'Beatrice', 'Teresa',
    ];

    private array $filipinoLastNames = [
        'Santos', 'Dela Cruz', 'Garcia', 'Rivera', 'Lopez', 'Martinez', 'Gonzales', 'Reyes', 'Torres', 'Flores',
    ];

    private array $barangays = [
        'Bahay Laya', 'Bitungol', 'Cataluran', 'Daang Laya', 'Dulong Bayan', 'Hulo', 'Kamalas', 'Katipunan',
        'Katigbakan', 'Layac', 'Lucia', 'Lumalandig', 'Magdalo', 'Mahabang Parang', 'Malaya', 'Mangahan',
    ];

    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $lastDelivery = fake()->optional(0.6)->dateTimeBetween('-5 years', '-1 months');
            $td1 = fake()->optional(0.8)->dateTimeBetween('-3 years', '-1 months');
            $td2 = fake()->optional(0.7)->dateTimeBetween('-3 years', '-1 months');
            $td3 = fake()->optional(0.5)->dateTimeBetween('-3 years', '-1 months');
            $td4 = fake()->optional(0.3)->dateTimeBetween('-3 years', '-1 months');
            $td5 = fake()->optional(0.1)->dateTimeBetween('-3 years', '-1 months');
            $tdl = fake()->optional(0.2)->dateTimeBetween('-3 years', '-1 months');

            $motherFirstName = fake()->randomElement($this->filipinoFirstNames);
            $motherLastName = fake()->randomElement($this->filipinoLastNames);
            $barangay = fake()->randomElement($this->barangays);
            $gravida = fake()->numberBetween(1, 8);
            $para = fake()->numberBetween(0, min($gravida - 1, 6));

            $record = PrenatalRecord::create([
                'record_no' => 'PRE-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'mother_name' => $motherFirstName . ' ' . $motherLastName,
                'purok' => 'Purok ' . fake()->numberBetween(1, 12),
                'age' => fake()->numberBetween(18, 45),
                'dob' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                'occupation' => fake()->optional(0.6)->randomElement(['Housewife', 'Farmer', 'Vendor', 'Laborer', 'Retail Staff']),
                'education' => fake()->optional()->randomElement(['Elementary', 'High School', 'College', 'Post-grad']),
                'is_4ps' => fake()->boolean(25),
                'four_ps_no' => fake()->optional(0.25)->numerify('4PS-########'),
                'cell' => '09' . fake()->numberBetween(100000000, 999999999),
                'lmp' => fake()->dateTimeBetween('-9 months', '-5 months')->format('Y-m-d'),
                'edc' => fake()->dateTimeBetween('+1 months', '+4 months')->format('Y-m-d'),
                'urinalysis' => fake()->optional(0.7)->randomElement(['Normal', 'Proteinuria', 'Glycosuria']),
                'gravida' => $gravida,
                'para' => $para,
                'abortion' => fake()->numberBetween(0, 2),
                'delivery_count' => $para,
                'last_delivery_date' => $lastDelivery ? $lastDelivery->format('Y-m-d') : null,
                'delivery_type' => fake()->optional(0.7)->randomElement(['NSD', 'CS', 'Assisted']),
                'hemoglobin_first' => fake()->optional(0.8)->randomFloat(1, 9, 13),
                'hemoglobin_second' => fake()->optional(0.7)->randomFloat(1, 9, 13),
                'blood_type' => fake()->optional(0.9)->randomElement(['A', 'B', 'AB', 'O']) . fake()->optional()->randomElement(['+', '-']),
                'urinalysis_protein' => fake()->optional(0.8)->randomElement(['Negative', 'Trace', '+1', '+2']),
                'urinalysis_sugar' => fake()->optional(0.8)->randomElement(['Negative', 'Trace', '+1', '+2']),
                'husband_name' => fake()->optional(0.85)->randomElement(['Juan', 'Jose', 'Carlos', 'Luis', 'Pedro', 'Miguel', 'Ramon', 'Antonio', 'Francisco', 'Manuel']) . ' ' . fake()->randomElement($this->filipinoLastNames),
                'husband_occupation' => fake()->optional(0.8)->randomElement(['Farmer', 'Driver', 'Construction Worker', 'Vendor', 'Laborer']),
                'husband_education' => fake()->optional(0.75)->randomElement(['Elementary', 'High School', 'College']),
                'family_religion' => fake()->optional(0.9)->randomElement(['Roman Catholic', 'INC', 'Born Again', 'SDA', 'Others']),
                'amount_prepared' => fake()->optional(0.7)->randomElement(['< 5,000', '5,000 - 10,000', '> 10,000']),
                'philhealth_member' => fake()->optional(0.85)->randomElement(['Yes', 'No']),
                'delivery_location' => fake()->optional(0.8)->randomElement(['BHS', 'RHUs', 'Hospital', 'Home']),
                'delivery_partner' => fake()->optional(0.85)->randomElement(['Midwife', 'Doctor', 'Hilot']),
                'td1' => $td1 ? $td1->format('Y-m-d') : null,
                'td2' => $td2 ? $td2->format('Y-m-d') : null,
                'td3' => $td3 ? $td3->format('Y-m-d') : null,
                'td4' => $td4 ? $td4->format('Y-m-d') : null,
                'td5' => $td5 ? $td5->format('Y-m-d') : null,
                'tdl' => $tdl ? $tdl->format('Y-m-d') : null,
                'fbs' => fake()->optional(0.6)->randomFloat(1, 80, 120),
                'rbs' => fake()->optional(0.5)->randomFloat(1, 80, 160),
                'ogtt' => fake()->optional(0.4)->randomFloat(1, 100, 180),
                'vdrl' => fake()->optional(0.05)->randomElement(['Reactive', 'Non-reactive']),
                'hbsag' => fake()->optional(0.08)->randomElement(['Reactive', 'Non-reactive']),
                'hiv' => fake()->optional(0.02)->randomElement(['Reactive', 'Non-reactive']),
                'extra' => [
                    'notes' => fake()->optional(0.3)->sentence(),
                ],
            ]);

            // 1-4 visits per prenatal record (more realistic for multiple visits)
            $visitCount = fake()->numberBetween(1, 4);
            for ($v = 0; $v < $visitCount; $v++) {
                PrenatalVisit::create([
                    'prenatal_record_id' => $record->id,
                    'date' => fake()->dateTimeBetween('-8 months', 'now')->format('Y-m-d'),
                    'trimester' => fake()->optional()->randomElement(['1st', '2nd', '3rd']),
                    'risk' => fake()->optional(0.7)->randomElement(['Low', 'Moderate', 'High']),
                    'first_visit' => $v === 0 ? 'Yes' : 'No',
                    'subjective' => fake()->optional(0.6)->sentence(),
                    'aog' => fake()->optional()->numberBetween(8, 40) . ' weeks',
                    'weight' => fake()->optional(0.9)->randomFloat(1, 45, 85),
                    'height' => fake()->optional(0.7)->randomFloat(1, 140, 175),
                    'bp' => fake()->optional(0.95)->numerify('1##/##'),
                    'pr' => fake()->optional(0.9)->numberBetween(65, 100),
                    'fh' => fake()->optional(0.8)->numerify('##'),
                    'fht' => fake()->optional(0.8)->numerify('1##'),
                    'presentation' => fake()->optional(0.7)->randomElement(['Cephalic', 'Breech', 'Transverse']),
                    'bmi' => fake()->optional(0.7)->randomFloat(1, 18, 35),
                    'rr' => fake()->optional(0.8)->numberBetween(16, 28),
                    'hr' => fake()->optional(0.9)->numberBetween(70, 110),
                    'assessment' => fake()->optional(0.6)->sentence(),
                    'plan' => fake()->optional(0.6)->sentence(),
                ]);
            }
        }
    }
}
