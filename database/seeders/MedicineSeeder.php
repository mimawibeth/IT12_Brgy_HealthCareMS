<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    private array $philippineMedicines = [
        // Pain relievers
        ['name' => 'Paracetamol', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],
        ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'dosage_form' => 'Tablet', 'strength' => '200 mg'],
        ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic Acid', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],
        ['name' => 'Mefenamic Acid', 'generic_name' => 'Mefenamic Acid', 'dosage_form' => 'Tablet', 'strength' => '250 mg'],

        // Antibiotics
        ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],
        ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'dosage_form' => 'Capsule', 'strength' => '250 mg'],
        ['name' => 'Amoxicillin Suspension', 'generic_name' => 'Amoxicillin', 'dosage_form' => 'Suspension', 'strength' => '125 mg/5 mL'],
        ['name' => 'Erythromycin', 'generic_name' => 'Erythromycin', 'dosage_form' => 'Tablet', 'strength' => '250 mg'],
        ['name' => 'Cephalexin', 'generic_name' => 'Cephalexin', 'dosage_form' => 'Capsule', 'strength' => '500 mg'],

        // Respiratory
        ['name' => 'Salbutamol Inhaler', 'generic_name' => 'Salbutamol', 'dosage_form' => 'Inhaler', 'strength' => '100 mcg/dose'],
        ['name' => 'Theophylline', 'generic_name' => 'Theophylline', 'dosage_form' => 'Tablet', 'strength' => '100 mg'],

        // Antacid & GI
        ['name' => 'Aluminum Hydroxide', 'generic_name' => 'Aluminum Hydroxide', 'dosage_form' => 'Suspension', 'strength' => '320 mg/5 mL'],
        ['name' => 'Ranitidine', 'generic_name' => 'Ranitidine', 'dosage_form' => 'Tablet', 'strength' => '150 mg'],
        ['name' => 'Metoclopramide', 'generic_name' => 'Metoclopramide', 'dosage_form' => 'Tablet', 'strength' => '10 mg'],

        // Cardiovascular
        ['name' => 'Amlodipine', 'generic_name' => 'Amlodipine', 'dosage_form' => 'Tablet', 'strength' => '5 mg'],
        ['name' => 'Lisinopril', 'generic_name' => 'Lisinopril', 'dosage_form' => 'Tablet', 'strength' => '10 mg'],
        ['name' => 'Atorvastatin', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tablet', 'strength' => '10 mg'],

        // Diabetes
        ['name' => 'Metformin', 'generic_name' => 'Metformin', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],
        ['name' => 'Metformin', 'generic_name' => 'Metformin', 'dosage_form' => 'Tablet', 'strength' => '1000 mg'],
        ['name' => 'Glibenclamide', 'generic_name' => 'Glibenclamide', 'dosage_form' => 'Tablet', 'strength' => '5 mg'],

        // Vitamins & Minerals
        ['name' => 'Vitamin C', 'generic_name' => 'Ascorbic Acid', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],
        ['name' => 'Multivitamin with Minerals', 'generic_name' => 'Multivitamin', 'dosage_form' => 'Tablet', 'strength' => 'Complex'],
        ['name' => 'Ferrous Sulfate', 'generic_name' => 'Ferrous Sulfate', 'dosage_form' => 'Tablet', 'strength' => '325 mg'],
        ['name' => 'Calcium Carbonate', 'generic_name' => 'Calcium Carbonate', 'dosage_form' => 'Tablet', 'strength' => '500 mg'],

        // Cough & Cold
        ['name' => 'Ambroxol', 'generic_name' => 'Ambroxol', 'dosage_form' => 'Syrup', 'strength' => '15 mg/5 mL'],
        ['name' => 'Loratadine', 'generic_name' => 'Loratadine', 'dosage_form' => 'Tablet', 'strength' => '10 mg'],
        ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine', 'dosage_form' => 'Tablet', 'strength' => '10 mg'],

        // Topical
        ['name' => 'Povidone Iodine Ointment', 'generic_name' => 'Povidone Iodine', 'dosage_form' => 'Ointment', 'strength' => '10%'],
        ['name' => 'Bacitracin Ointment', 'generic_name' => 'Bacitracin', 'dosage_form' => 'Ointment', 'strength' => '500 units/g'],
        ['name' => 'Hydrocortisone Cream', 'generic_name' => 'Hydrocortisone', 'dosage_form' => 'Cream', 'strength' => '1%'],

        // Injections
        ['name' => 'Tetanus Toxoid', 'generic_name' => 'Tetanus Toxoid', 'dosage_form' => 'Injection', 'strength' => '0.5 mL'],
        ['name' => 'BCG Vaccine', 'generic_name' => 'BCG Vaccine', 'dosage_form' => 'Injection', 'strength' => '0.05 mL'],
        ['name' => 'Vitamin B12', 'generic_name' => 'Cyanocobalamin', 'dosage_form' => 'Injection', 'strength' => '1000 mcg/mL'],
    ];

    public function run(): void
    {
        foreach ($this->philippineMedicines as $medicine) {
            for ($i = 0; $i < 3; $i++) { // Create 3 batches of each medicine
                Medicine::create([
                    'name' => $medicine['name'],
                    'generic_name' => $medicine['generic_name'],
                    'dosage_form' => $medicine['dosage_form'],
                    'strength' => $medicine['strength'],
                    'unit' => in_array($medicine['dosage_form'], ['Injection', 'Inhaler']) ? 'vial' : (in_array($medicine['dosage_form'], ['Syrup', 'Suspension', 'Ointment', 'Cream']) ? 'bottle' : 'tablet'),
                    'reorder_level' => fake()->numberBetween(30, 150),
                    'remarks' => 'Essential medicine - ' . ($i === 0 ? 'Primary supply' : ($i === 1 ? 'Secondary supply' : 'Reserve supply')),
                ]);
            }
        }
    }
}
