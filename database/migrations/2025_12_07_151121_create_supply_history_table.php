<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supply_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_supply_id')->constrained('medical_supplies')->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('received_from')->nullable();
            $table->date('date_received');
            $table->string('handled_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_history');
    }
};
