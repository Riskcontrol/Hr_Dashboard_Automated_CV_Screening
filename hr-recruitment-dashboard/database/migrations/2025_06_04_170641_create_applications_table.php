<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('phone')->nullable();
            $table->string('cv_original_name');
            $table->string('cv_stored_path');
            $table->integer('cv_file_size');
            $table->longText('extracted_text')->nullable();
            $table->foreignId('keyword_set_id')->nullable()->constrained('keyword_sets');
            $table->enum('qualification_status', ['qualified', 'not_qualified', 'pending', 'failed'])->default('pending');
            $table->decimal('match_percentage', 5, 2)->default(0.00);
            $table->json('found_keywords')->nullable();
            $table->json('missing_keywords')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['qualification_status', 'processed_at']);
            $table->index('applicant_email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};