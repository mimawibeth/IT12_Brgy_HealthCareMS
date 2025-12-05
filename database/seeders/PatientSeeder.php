<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    private array $filipinoFirstNames = [
        'Maria', 'Juan', 'Rosa', 'Jose', 'Ana', 'Manuel', 'Carmen', 'Luis', 'Sofia', 'Miguel',
        'Angeles', 'Pedro', 'Gloria', 'Francisco', 'Elena', 'Fernando', 'Catalina', 'Ramon', 'Lucia', 'Carlos',
        'Magdalena', 'Antonio', 'Theresa', 'Alejandro', 'Esperanza', 'Ricardo', 'Victoria', 'Roberto', 'Josefina', 'Felipe',
    ];

    private array $filipinoLastNames = [
        'Santos', 'Dela Cruz', 'Garcia', 'Rivera', 'Lopez', 'Martinez', 'Gonzales', 'Reyes', 'Torres', 'Flores',
        'Morales', 'Gutierrez', 'Ramos', 'Silva', 'Camacho', 'Navarro', 'Villanueva', 'Salas', 'Medina', 'Fernandez',
        'Aguilar', 'Montoya', 'Vargas', 'Castillo', 'Molina', 'Acosta', 'Peralta', 'Nunez', 'Salazar', 'Bautista',
    ];

    private array $barangays = [
        'Bahay Laya', 'Bitungol', 'Cataluran', 'Daang Laya', 'Dulong Bayan', 'Hulo', 'Kamalas', 'Katipunan',
        'Katigbakan', 'Layac', 'Lucia', 'Lumalandig', 'Magdalo', 'Mahabang Parang', 'Malaya', 'Mangahan',
        'Mangga', 'Mapulang Lupa', 'Mataas na Laya', 'Mauhay', 'Maybunga', 'Mayuga', 'Nayong Kanluran', 'Nayong Silangan',
        'Pag-asa', 'Palanglupa', 'Palung Lupa', 'Palsiguran', 'Pamulang', 'Pariahan', 'Paso de Blas', 'Pinagsama',
    ];

    private array $occupations = [
        'Farmer', 'Housewife', 'Vendor', 'Tricycle Driver', 'Construction Worker', 'Teacher', 'Healthcare Worker',
        'Fisherman', 'Security Guard', 'Retail Staff', 'Laborer', 'Self-employed', 'Driver', 'Caregiver', 'Mechanic',
    ];

    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $sex = fake()->randomElement(['M', 'F']);
            $firstName = fake()->randomElement($this->filipinoFirstNames);
            $lastName = fake()->randomElement($this->filipinoLastNames);
            $barangay = fake()->randomElement($this->barangays);

            $birthday = fake()->dateTimeBetween('-80 years', '-1 years')->format('Y-m-d');
            $dateRegistered = fake()->dateTimeBetween($birthday, 'now')->format('Y-m-d');

            $height = fake()->randomFloat(2, 140, 190);
            $weight = fake()->randomFloat(2, 40, 100);
            $bmi = $weight / pow($height / 100, 2);

            // Philippine health conditions - more common in Philippines
            $diabetesDate = fake()->optional(0.25)->dateTimeBetween('-10 years', 'now');
            $hypertensionDate = fake()->optional(0.25)->dateTimeBetween('-10 years', 'now');
            $copdDate = fake()->optional(0.08)->dateTimeBetween('-10 years', 'now');
            $asthmaDate = fake()->optional(0.15)->dateTimeBetween('-10 years', 'now');
            $cataractDate = fake()->optional(0.08)->dateTimeBetween('-10 years', 'now');
            $eorDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $diabeticRetinopathyDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $otherEyeDiseaseDate = fake()->optional(0.08)->dateTimeBetween('-10 years', 'now');
            $alcoholismDate = fake()->optional(0.08)->dateTimeBetween('-10 years', 'now');
            $substanceAbuseDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $otherMentalDisordersDate = fake()->optional(0.05)->dateTimeBetween('-10 years', 'now');
            $atRiskSuicideDate = fake()->optional(0.02)->dateTimeBetween('-10 years', 'now');
            $philpenDate = fake()->optional()->dateTimeBetween('-5 years', 'now');
            $whoDasDate = fake()->optional()->dateTimeBetween('-5 years', 'now');

            Patient::create([
                'dateRegistered' => $dateRegistered,
                'patientNo' => 'PT-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'sex' => $sex,
                'name' => $firstName . ' ' . $lastName,
                'birthday' => $birthday,
                'contactNumber' => '09' . fake()->numberBetween(100000000, 999999999),
                'address' => fake()->numberBetween(1, 999) . ' ' . fake()->streetName() . ', ' . $barangay,
                'nhtsIdNo' => fake()->optional()->numerify('NHTS-########'),
                'pwdIdNo' => fake()->optional(0.1)->numerify('PWD-#######'),
                'phicIdNo' => fake()->optional(0.7)->numerify('PHIC-##########'),
                'fourPsCctIdNo' => fake()->optional(0.2)->numerify('4PS-########'),
                'ethnicGroup' => fake()->optional()->randomElement(['Tagalog', 'Bisaya', 'Ilocano', 'Pangasinense', 'Bicolano', 'Kapampangan', 'Others']),
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
