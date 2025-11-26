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
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nip_records');
    }
};
