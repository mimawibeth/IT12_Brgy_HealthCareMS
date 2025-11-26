<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prenatal_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_no')->nullable();
            $table->string('mother_name');
            $table->string('purok')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();
            $table->boolean('is_4ps')->default(false);
            $table->string('four_ps_no')->nullable();
            $table->string('cell')->nullable();
            $table->date('lmp')->nullable();
            $table->date('edc')->nullable();
            $table->string('urinalysis')->nullable();
            $table->unsignedTinyInteger('gravida')->nullable();
            $table->unsignedTinyInteger('para')->nullable();
            $table->unsignedTinyInteger('abortion')->nullable();
            $table->unsignedTinyInteger('delivery_count')->nullable();
            $table->date('last_delivery_date')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('hemoglobin_first')->nullable();
            $table->string('hemoglobin_second')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('urinalysis_protein')->nullable();
            $table->string('urinalysis_sugar')->nullable();
            $table->string('husband_name')->nullable();
            $table->string('husband_occupation')->nullable();
            $table->string('husband_education')->nullable();
            $table->string('family_religion')->nullable();
            $table->string('amount_prepared')->nullable();
            $table->string('philhealth_member')->nullable();
            $table->string('delivery_location')->nullable();
            $table->string('delivery_partner')->nullable();
            $table->date('td1')->nullable();
            $table->date('td2')->nullable();
            $table->date('td3')->nullable();
            $table->date('td4')->nullable();
            $table->date('td5')->nullable();
            $table->date('tdl')->nullable();
            $table->string('fbs')->nullable();
            $table->string('rbs')->nullable();
            $table->string('ogtt')->nullable();
            $table->string('vdrl')->nullable();
            $table->string('hbsag')->nullable();
            $table->string('hiv')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prenatal_records');
    }
};
