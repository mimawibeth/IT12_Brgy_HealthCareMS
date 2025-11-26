<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_planning_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_no')->nullable();
            $table->string('client_name');
            $table->date('dob')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('occupation')->nullable();
            $table->string('spouse_name')->nullable();
            $table->unsignedTinyInteger('spouse_age')->nullable();
            $table->unsignedTinyInteger('children_count')->nullable();
            $table->string('client_type')->nullable();
            $table->json('reason')->nullable();
            $table->json('medical_history')->nullable();
            $table->unsignedTinyInteger('gravida')->nullable();
            $table->unsignedTinyInteger('para')->nullable();
            $table->date('last_delivery')->nullable();
            $table->date('last_period')->nullable();
            $table->string('menstrual_flow')->nullable();
            $table->string('dysmenorrhea')->nullable();
            $table->json('sti_risk')->nullable();
            $table->json('vaw_risk')->nullable();
            $table->string('bp')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->text('exam_findings')->nullable();
            $table->string('counseled_by')->nullable();
            $table->string('client_signature')->nullable();
            $table->date('consent_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_planning_records');
    }
};
