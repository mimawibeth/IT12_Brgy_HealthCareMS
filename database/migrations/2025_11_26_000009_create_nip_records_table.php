<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nip_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_no')->nullable();
            $table->date('date')->nullable();
            $table->string('child_name');
            $table->date('dob');
            $table->string('address');
            $table->string('mother_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('contact')->nullable();
            $table->string('place_delivery')->nullable();
            $table->string('attended_by')->nullable();
            $table->string('sex_baby', 1)->nullable();
            $table->string('nhts_4ps_id')->nullable();
            $table->string('phic_id')->nullable();
            $table->string('tt_status_mother')->nullable();
            $table->string('birth_length')->nullable();
            $table->string('birth_weight')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('initiated_breastfeeding')->nullable();
            $table->unsignedTinyInteger('birth_order')->nullable();
            $table->date('newborn_screening_date')->nullable();
            $table->string('newborn_screening_result')->nullable();
            $table->string('hearing_test_screened')->nullable();
            $table->string('vit_k')->nullable();
            $table->string('bcg')->nullable();
            $table->string('hepa_b_24h')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nip_records');
    }
};
