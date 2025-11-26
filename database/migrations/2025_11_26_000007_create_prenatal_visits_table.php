<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prenatal_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prenatal_record_id')->constrained('prenatal_records')->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->string('trimester')->nullable();
            $table->string('risk')->nullable();
            $table->string('first_visit')->nullable();
            $table->text('subjective')->nullable();
            $table->string('aog')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('bp')->nullable();
            $table->string('pr')->nullable();
            $table->string('fh')->nullable();
            $table->string('fht')->nullable();
            $table->string('presentation')->nullable();
            $table->string('bmi')->nullable();
            $table->string('rr')->nullable();
            $table->string('hr')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prenatal_visits');
    }
};
