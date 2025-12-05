<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // BHW who made the request
            $table->string('type'); // Type of financial assistance
            $table->text('reason'); // Reason for the request
            $table->decimal('amount', 10, 2)->nullable(); // Amount requested
            $table->text('description')->nullable(); // Additional details
            
            // Status and approval tracking
            $table->enum('status', ['pending', 'approved_by_admin', 'rejected_by_admin', 'approved_by_superadmin', 'rejected_by_superadmin'])->default('pending');
            $table->unsignedBigInteger('admin_id')->nullable(); // Admin who reviewed
            $table->unsignedBigInteger('superadmin_id')->nullable(); // Superadmin who approved/rejected
            $table->text('admin_notes')->nullable(); // Notes from admin
            $table->text('superadmin_notes')->nullable(); // Notes from superadmin
            
            // Timestamps
            $table->timestamp('submitted_at')->useCurrent(); // When BHW submitted
            $table->timestamp('admin_reviewed_at')->nullable(); // When admin reviewed
            $table->timestamp('superadmin_reviewed_at')->nullable(); // When superadmin reviewed
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('superadmin_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
            $table->index('user_id');
            $table->index('admin_id');
            $table->index('superadmin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_assistance_requests');
    }
};
