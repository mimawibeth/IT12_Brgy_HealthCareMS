<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nip_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nip_record_id')->constrained('nip_records')->cascadeOnDelete();
            $table->date('visit_date')->nullable();
            $table->unsignedTinyInteger('age_months')->nullable();
            $table->string('weight')->nullable();
            $table->string('length')->nullable();
            $table->string('status')->nullable();
            $table->string('breastfeeding')->nullable();
            $table->string('temperature')->nullable();
            $table->string('vaccine')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nip_visits');
    }
};
