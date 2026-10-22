<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('keyword_sets', function (Blueprint $table) {
            // Add columns without specifying position (after clause)
            if (!Schema::hasColumn('keyword_sets', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('keyword_sets', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            if (!Schema::hasColumn('keyword_sets', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
        });

        // Add foreign key constraint separately
        try {
            Schema::table('keyword_sets', function (Blueprint $table) {
                if (Schema::hasColumn('keyword_sets', 'created_by') && Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist or fail, that's ok
        }

        // Update any existing records
        $firstUser = \App\Models\User::first();
        if ($firstUser && Schema::hasColumn('keyword_sets', 'created_by')) {
            \DB::table('keyword_sets')
                ->whereNull('created_by')
                ->update(['created_by' => $firstUser->id]);
        }
    }

    public function down()
    {
        Schema::table('keyword_sets', function (Blueprint $table) {
            // Drop foreign key first
            try {
                $table->dropForeign(['created_by']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            // Drop columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('keyword_sets', 'created_by')) {
                $columnsToDrop[] = 'created_by';
            }
            if (Schema::hasColumn('keyword_sets', 'is_active')) {
                $columnsToDrop[] = 'is_active';
            }
            if (Schema::hasColumn('keyword_sets', 'description')) {
                $columnsToDrop[] = 'description';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};