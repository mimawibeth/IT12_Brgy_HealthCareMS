<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $sex = fake()->randomElement(['M', 'F']);

            $birthday = fake()->dateTimeBetween('-80 years', '-1 years')->format('Y-m-d');
            $dateRegistered = fake()->dateTimeBetween($birthday, 'now')->format('Y-m-d');

            $height = fake()->randomFloat(2, 140, 190);
            $weight = fake()->randomFloat(2, 40, 100);
            $bmi = $weight / pow($height / 100, 2);

            $diabetesDate = fake()->optional(0.2)->dateTimeBetween('-10 years', 'now');
            $hypertensionDate = fake()->optional(0.2)->dateTimeBetween('-10 years', 'now');
            $copdDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $asthmaDate = fake()->optional(0.1)->dateTimeBetween('-10 years', 'now');
            $cataractDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $eorDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $diabeticRetinopathyDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $otherEyeDiseaseDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $alcoholismDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $substanceAbuseDate = fake()->optional(0.03)->dateTimeBetween('-10 years', 'now');
            $otherMentalDisordersDate = fake()->optional(0.03)->dateTimeBetween('-10 years', 'now');
            $atRiskSuicideDate = fake()->optional(0.01)->dateTimeBetween('-10 years', 'now');
            $philpenDate = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $whoDasDate = fake()->optional()->dateTimeBetween('-5 years', 'now');

            Patient::create([
                'dateRegistered' => $dateRegistered,
                'patientNo' => 'PT-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'sex' => $sex,
                'name' => fake()->name($sex === 'M' ? 'male' : 'female'),
                'birthday' => $birthday,
                'contactNumber' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'nhtsIdNo' => fake()->optional()->numerify('NHTS-########'),
                'pwdIdNo' => fake()->optional(0.1)->numerify('PWD-#######'),
                'phicIdNo' => fake()->optional()->numerify('PHIC-##########'),
                'fourPsCctIdNo' => fake()->optional()->numerify('4PS-########'),
                'ethnicGroup' => fake()->optional()->randomElement(['Tagalog', 'Bisaya', 'Ilocano', 'Others']),
                'diabetesDate' => $diabetesDate ? $diabetesDate->format('Y-m-d') : null,
                'hypertensionDate' => $hypertensionDate ? $hypertensionDate->format('Y-m-d') : null,
                'copdDate' => $copdDate ? $copdDate->format('Y-m-d') : null,
                'asthmaDate' => $asthmaDate ? $asthmaDate->format('Y-m-d') : null,
                'cataractDate' => $cataractDate ? $cataractDate->format('Y-m-d') : null,
                'eorDate' => $eorDate ? $eorDate->format('Y-m-d') : null,
                'diabeticRetinopathyDate' => $diabeticRetinopathyDate ? $diabeticRetinopathyDate->format('Y-m-d') : null,
                'otherEyeDiseaseDate' => $otherEyeDiseaseDate ? $otherEyeDiseaseDate->format('Y-m-d') : null,
                'alcoholismDate' => $alcoholismDate ? $alcoholismDate->format('Y-m-d') : null,
                'substanceAbuseDate' => $substanceAbuseDate ? $substanceAbuseDate->format('Y-m-d') : null,
                'otherMentalDisordersDate' => $otherMentalDisordersDate ? $otherMentalDisordersDate->format('Y-m-d') : null,
                'atRiskSuicideDate' => $atRiskSuicideDate ? $atRiskSuicideDate->format('Y-m-d') : null,
                'philpenDate' => $philpenDate ? $philpenDate->format('Y-m-d') : null,
                'currentSmoker' => fake()->boolean(20),
                'passiveSmoker' => fake()->boolean(30),
                'stoppedSmoking' => fake()->boolean(10),
                'drinksAlcohol' => fake()->boolean(40),
                'hadFiveDrinks' => fake()->boolean(15),
                'dietaryRiskFactors' => fake()->boolean(40),
                'physicalInactivity' => fake()->boolean(35),
                'height' => $height,
                'weight' => $weight,
                'waistCircumference' => fake()->randomFloat(2, 60, 120),
                'bmi' => round($bmi, 2),
                'whoDasDate' => $whoDasDate ? $whoDasDate->format('Y-m-d') : null,
                'part1' => fake()->optional()->randomElement(['No disability', 'Mild', 'Moderate', 'Severe']),
                'part2Score' => fake()->optional()->numberBetween(0, 48),
                'top1Domain' => fake()->optional()->word(),
                'top2Domain' => fake()->optional()->word(),
                'top3Domain' => fake()->optional()->word(),
                'lengthDiabetes' => fake()->optional()->randomElement(['1 year', '2 years', '5 years', '10 years']),
                'lengthHypertension' => fake()->optional()->randomElement(['6 months', '1 year', '3 years', '8 years']),
                'floaters' => fake()->boolean(10),
                'blurredVision' => fake()->boolean(20),
                'fluctuatingVision' => fake()->boolean(10),
                'impairedColorVision' => fake()->boolean(5),
                'darkEmptyAreas' => fake()->boolean(5),
                'visionLoss' => fake()->boolean(3),
                'visualAcuityLeft' => fake()->optional()->randomElement(['20/20', '20/30', '20/40', '20/60']),
                'visualAcuityRight' => fake()->optional()->randomElement(['20/20', '20/30', '20/40', '20/60']),
                'ophthalmoscopyResults' => fake()->optional()->sentence(),
            ]);
        }
    }
}
