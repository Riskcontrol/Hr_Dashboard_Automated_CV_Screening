<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keyword_sets', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->json('keywords');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['job_title', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('keyword_sets');
    }
};