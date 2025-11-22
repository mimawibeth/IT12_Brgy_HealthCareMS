<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Foreign key to link with patients table
            $table->unsignedBigInteger('PatientID');

            // Visit information
            $table->date('date')->nullable(); // Date of the assessment
            $table->string('age')->nullable(); // Patient's age at time of assessment
            $table->string('cvdRisk')->nullable(); // Cardiovascular disease risk assessment

            // Vital signs
            $table->string('bpSystolic')->nullable(); // Blood pressure - systolic (upper number)
            $table->string('bpDiastolic')->nullable(); // Blood pressure - diastolic (lower number)
            $table->string('wt')->nullable(); // Weight in kilograms
            $table->string('ht')->nullable(); // Height in centimeters

            // Laboratory results
            $table->string('fbsRbs')->nullable(); // Fasting/random blood sugar level
            $table->string('lipidProfile')->nullable(); // Cholesterol and triglycerides levels
            $table->string('urineKetones')->nullable(); // Presence of ketones in urine
            $table->string('urineProtein')->nullable(); // Presence of protein in urine
            $table->string('footCheck')->nullable(); // Results of foot examination

            // Clinical notes
            $table->text('chiefComplaint')->nullable(); // Main reason for visit
            $table->text('historyPhysical')->nullable(); // Findings from history and physical exam
            $table->text('management')->nullable(); // Treatment plan and recommendations

            $table->timestamps();

            $table->foreign('PatientID')
                ->references('PatientID')
                ->on('patients')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
