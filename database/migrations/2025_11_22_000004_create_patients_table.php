<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('PatientID'); // Primary key for the patient record

            $table->date('dateRegistered'); // Date when the patient was first registered
            $table->string('patientNo')->nullable(); // Unique identifier/number for the patient
            $table->enum('sex', ['M', 'F']); // Patient's biological sex (M for Male, F for Female)
            $table->string('name'); // Full name of the patient
            $table->date('birthday'); // Patient's date of birth
            $table->string('contactNumber')->nullable(); // Contact phone number
            $table->text('address'); // Complete residential address

            // Government IDs and memberships
            $table->string('nhtsIdNo')->nullable(); // National Household Targeting System ID
            $table->string('pwdIdNo')->nullable(); // Person with Disability ID
            $table->string('phicIdNo')->nullable(); // Philippine Health Insurance Corporation ID
            $table->string('fourPsCctIdNo')->nullable(); // Pantawid Pamilyang Pilipino Program ID
            $table->string('ethnicGroup')->nullable(); // Ethnic/indigenous group, if applicable

            // Dates of diagnosis for various conditions
            $table->date('diabetesDate')->nullable(); // Date of diabetes diagnosis
            $table->date('hypertensionDate')->nullable(); // Date of hypertension diagnosis
            $table->date('copdDate')->nullable(); // Date of Chronic Obstructive Pulmonary Disease diagnosis
            $table->date('asthmaDate')->nullable(); // Date of asthma diagnosis
            $table->date('cataractDate')->nullable(); // Date of cataract diagnosis
            $table->date('eorDate')->nullable(); // Date of Eye, Ear, Nose, and Throat (EENT) condition diagnosis
            $table->date('diabeticRetinopathyDate')->nullable(); // Date of diabetic retinopathy diagnosis
            $table->date('otherEyeDiseaseDate')->nullable(); // Date of other eye disease diagnosis
            $table->date('alcoholismDate')->nullable(); // Date of alcoholism diagnosis
            $table->date('substanceAbuseDate')->nullable(); // Date of substance abuse diagnosis
            $table->date('otherMentalDisordersDate')->nullable(); // Date of other mental health disorder diagnosis
            $table->date('atRiskSuicideDate')->nullable(); // Date when identified as at risk for suicide

            // PhilPen (Philippine Package of Essential Non-communicable Disease Interventions) data
            $table->date('philpenDate')->nullable(); // Date of PhilPen screening
            $table->boolean('currentSmoker')->default(false); // Currently smokes tobacco
            $table->boolean('passiveSmoker')->default(false); // Exposed to secondhand smoke
            $table->boolean('stoppedSmoking')->default(false); // Former smoker
            $table->boolean('drinksAlcohol')->default(false); // Consumes alcoholic beverages
            $table->boolean('hadFiveDrinks')->default(false); // Has 5+ drinks in one occasion monthly
            $table->boolean('dietaryRiskFactors')->default(false); // Has unhealthy dietary habits
            $table->boolean('physicalInactivity')->default(false); // Lacks sufficient physical activity
            
            // Anthropometric measurements
            $table->decimal('height', 5, 2)->nullable(); // Height in centimeters
            $table->decimal('weight', 5, 2)->nullable(); // Weight in kilograms
            $table->decimal('waistCircumference', 5, 2)->nullable(); // Waist circumference in cm
            $table->decimal('bmi', 5, 2)->nullable(); // Body Mass Index (weight in kg/height in m²)

            // WHO Disability Assessment Schedule (WHODAS) data
            $table->date('whoDasDate')->nullable(); // Date of WHODAS assessment
            $table->string('part1')->nullable(); // WHODAS Part 1 assessment result
            $table->integer('part2Score')->nullable(); // WHODAS Part 2 total disability score
            $table->string('top1Domain')->nullable(); // Primary domain of disability
            $table->string('top2Domain')->nullable(); // Secondary domain of disability
            $table->string('top3Domain')->nullable(); // Tertiary domain of disability

            // Medical history and current symptoms
            $table->string('lengthDiabetes')->nullable(); // Duration of diabetes condition
            $table->string('lengthHypertension')->nullable(); // Duration of hypertension
            
            // Visual symptoms (checkboxes)
            $table->boolean('floaters')->default(false); // Experiences floaters in vision
            $table->boolean('blurredVision')->default(false); // Has blurred vision
            $table->boolean('fluctuatingVision')->default(false); // Vision changes frequently
            $table->boolean('impairedColorVision')->default(false); // Difficulty distinguishing colors
            $table->boolean('darkEmptyAreas')->default(false); // Sees dark/empty spots
            $table->boolean('visionLoss')->default(false); // Has partial/total vision loss
            
            // Eye examination results
            $table->string('visualAcuityLeft')->nullable(); // Visual acuity measurement (left eye)
            $table->string('visualAcuityRight')->nullable(); // Visual acuity measurement (right eye)
            $table->text('ophthalmoscopyResults')->nullable(); // Findings from eye examination

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
