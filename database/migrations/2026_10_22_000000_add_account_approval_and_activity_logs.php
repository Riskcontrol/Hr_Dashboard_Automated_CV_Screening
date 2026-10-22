<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Existing accounts retain access; public registration explicitly sets pending.
            $table->string('status')->default('active')->index();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name');
            $table->string('user_role');
            $table->string('action')->index();
            $table->string('module')->index();
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->enum('qualification_status', ['qualified', 'not_qualified', 'pending', 'failed', 'Fairly Qualified'])
                ->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'approved_at', 'approved_by']);
        });
        // Keep the additional qualification value to preserve existing application data.
    }
};
