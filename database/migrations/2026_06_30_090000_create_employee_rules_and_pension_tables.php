<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['handbook', 'code_of_conduct']);
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('uploaded_by');
            $table->boolean('notify_staff')->default(true);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pension_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();
            $table->string('pension_provider')->nullable();
            $table->string('pension_pin')->nullable();
            $table->string('rsa_number')->nullable();
            $table->decimal('employee_contribution', 8, 2)->nullable();
            $table->decimal('employer_contribution', 8, 2)->nullable();
            $table->enum('contribution_type', ['percentage', 'fixed'])->default('percentage');
            $table->date('enrollment_date')->nullable();
            $table->enum('status', ['active', 'suspended', 'exited'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique('staff_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension_records');
        Schema::dropIfExists('employee_rules');
    }
};