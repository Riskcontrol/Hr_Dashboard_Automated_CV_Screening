<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('ai_evaluation')->nullable()->after('processed_at');
            $table->integer('ai_score')->nullable()->after('ai_evaluation');
            $table->json('ai_strengths')->nullable()->after('ai_score');
            $table->json('ai_weaknesses')->nullable()->after('ai_strengths');
            $table->text('ai_recommendation')->nullable()->after('ai_weaknesses');
            $table->timestamp('ai_evaluated_at')->nullable()->after('ai_recommendation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'ai_evaluation',
                'ai_score',
                'ai_strengths',
                'ai_weaknesses',
                'ai_recommendation',
                'ai_evaluated_at'
            ]);
        });
    }
};
