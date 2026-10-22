<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the kpis table.
 * Timestamp 054853 — runs AFTER staff_profiles (054851) and leave_requests (054852).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('department')->nullable();
            $table->enum('period_type', ['monthly', 'quarterly', 'annually']);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('target_value', 12, 2);
            $table->decimal('actual_value', 12, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->enum('rating', ['excellent', 'good', 'average', 'poor', 'critical'])->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamps();
            $table->index(['staff_profile_id', 'status']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};