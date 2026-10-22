<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->unsignedSmallInteger('appraisal_year')->default(date('Y'))->after('appraisal_type');
            $table->enum('form_type', ['self_evaluation','probation','annual'])->default('annual')->after('appraisal_year');
            $table->timestamp('sent_to_employee_at')->nullable()->after('reminder_sent_at');
            $table->text('self_mission_statement')->nullable()->after('sent_to_employee_at');
            $table->text('self_duties_understanding')->nullable();
            $table->text('self_job_achievements')->nullable();
            $table->text('self_other_achievements')->nullable();
            $table->text('self_likes_dislikes')->nullable();
            $table->text('self_most_difficult')->nullable();
            $table->text('self_improvement_actions')->nullable();
            $table->string('prob_professionalism', 1)->nullable();
            $table->text('prob_professionalism_comments')->nullable();
            $table->string('prob_crisis_management', 1)->nullable();
            $table->text('prob_crisis_management_comments')->nullable();
            $table->string('prob_quality_of_work', 1)->nullable();
            $table->text('prob_quality_of_work_comments')->nullable();
            $table->string('prob_dependability', 1)->nullable();
            $table->text('prob_dependability_comments')->nullable();
            $table->string('prob_team_spirit', 1)->nullable();
            $table->text('prob_team_spirit_comments')->nullable();
            $table->string('prob_result_orientation', 1)->nullable();
            $table->text('prob_result_orientation_comments')->nullable();
            $table->string('prob_followership', 1)->nullable();
            $table->text('prob_followership_comments')->nullable();
            $table->string('prob_self_discipline', 1)->nullable();
            $table->text('prob_self_discipline_comments')->nullable();
            $table->string('prob_organisation_planning', 1)->nullable();
            $table->text('prob_organisation_planning_comments')->nullable();
            $table->string('prob_self_development', 1)->nullable();
            $table->text('prob_self_development_comments')->nullable();
            $table->text('prob_skill_deficiencies')->nullable();
            $table->text('prob_constraints')->nullable();
            $table->text('prob_appraisee_comments')->nullable();
            $table->text('prob_observer_comments')->nullable();
            $table->text('prob_hod_comments')->nullable();
            $table->json('edit_history')->nullable()->after('sent_to_employee_at');
        });
    }

    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn([
                'appraisal_year','form_type','sent_to_employee_at',
                'self_mission_statement','self_duties_understanding','self_job_achievements',
                'self_other_achievements','self_likes_dislikes','self_most_difficult',
                'self_improvement_actions',
                'prob_professionalism','prob_professionalism_comments',
                'prob_crisis_management','prob_crisis_management_comments',
                'prob_quality_of_work','prob_quality_of_work_comments',
                'prob_dependability','prob_dependability_comments',
                'prob_team_spirit','prob_team_spirit_comments',
                'prob_result_orientation','prob_result_orientation_comments',
                'prob_followership','prob_followership_comments',
                'prob_self_discipline','prob_self_discipline_comments',
                'prob_organisation_planning','prob_organisation_planning_comments',
                'prob_self_development','prob_self_development_comments',
                'prob_skill_deficiencies','prob_constraints',
                'prob_appraisee_comments','prob_observer_comments','prob_hod_comments',
                'edit_history',
            ]);
        });
    }
};