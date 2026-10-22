<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appraisal_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('day_number');
            $table->enum('session', ['morning','afternoon']);
            $table->string('appraisee_name');
            $table->foreignId('appraisee_staff_id')->nullable()->constrained('staff_profiles')->nullOnDelete();
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue')->default('Teams');
            $table->string('appraiser_name');
            $table->string('observer_names')->nullable();
            $table->string('prepared_by')->nullable();
            $table->date('prepared_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->timestamps();
            $table->index(['year','scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisal_schedules');
    }
};