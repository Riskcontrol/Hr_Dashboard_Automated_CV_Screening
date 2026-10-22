<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Appraisal;
use App\Models\LeaveRequest;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function staff(User $user): StaffProfile
    {
        return StaffProfile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'employee_id' => 'EMP'.$user->id,
        ]);
    }

    public function test_admin_pages_render_with_new_schema(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = $this->staff(User::factory()->create());
        ActivityLog::record('login', 'Authentication', 'Test login');
        $this->actingAs($admin);

        foreach (['dashboard', 'staff', 'staff/create', "staff/{$staff->id}", "staff/{$staff->id}/edit",
            'leave', 'leave/create', 'kpis', 'kpis/create', 'compliance', 'compliance/create', 'appraisals', 'appraisals/create',
            'appraisal-schedule', 'attendance', 'attendance/create', 'exit-reports', 'exit-reports/create',
            'employee-rules', 'pension', 'pension/create', 'users', 'users/own-password', 'activity-log',
            'applications', 'keyword-sets'] as $path) {
            $this->get('/admin/'.$path)->assertOk();
        }
    }

    public function test_pending_and_suspended_accounts_cannot_log_in(): void
    {
        foreach (['pending', 'suspended'] as $status) {
            $user = User::factory()->create(['status' => $status]);
            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_suspension_revokes_an_existing_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $user->update(['status' => 'suspended']);
        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_hr_can_approve_a_pending_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['status' => 'pending']);
        $this->actingAs($admin)->post("/admin/users/{$user->id}/approve")->assertRedirect();
        $this->assertSame('active', $user->fresh()->status);
        $this->assertNotNull($user->fresh()->approved_at);
    }

    public function test_staff_portal_and_self_evaluation_are_scoped_to_the_employee(): void
    {
        $user = User::factory()->create();
        $staff = $this->staff($user);
        $other = $this->staff(User::factory()->create());
        $appraisal = Appraisal::create([
            'staff_profile_id' => $staff->id, 'appraisal_type' => 'annual',
            'due_date' => now()->addWeek(), 'reviewer_name' => 'HR', 'sent_to_employee_at' => now(),
        ]);
        $otherAppraisal = $appraisal->replicate();
        $otherAppraisal->staff_profile_id = $other->id;
        $otherAppraisal->save();
        $this->actingAs($user);
        foreach (['', '/leave', '/leave/create', '/appraisals', '/employee-rules', '/password',
            "/appraisals/{$appraisal->id}", "/appraisals/{$appraisal->id}/fill"] as $path) {
            $this->get('/my'.$path)->assertOk();
        }
        $this->post("/my/appraisals/{$appraisal->id}/save", ['self_mission_statement' => 'My goals'])
            ->assertRedirect();
        $this->assertSame('My goals', $appraisal->fresh()->self_mission_statement);
        $this->get("/my/appraisals/{$otherAppraisal->id}/fill")->assertForbidden();
        $this->post("/my/appraisals/{$otherAppraisal->id}/save", [])->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
    }

    public function test_approved_leave_cannot_be_edited_or_reviewed_again(): void
    {
        $staff = $this->staff(User::factory()->create());
        $leave = LeaveRequest::create([
            'staff_profile_id' => $staff->id, 'leave_type' => 'annual',
            'start_date' => '2026-11-02', 'end_date' => '2026-11-03', 'total_days' => 2,
            'approver_type' => 'hr', 'approver_name' => 'HR', 'status' => 'approved',
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->put("/admin/leave/{$leave->id}", [])->assertForbidden();
        $this->post("/admin/leave/{$leave->id}/reject", [])->assertForbidden();
    }
    public function test_leave_creation_pension_and_filters_work(): void
    {
        $staff = $this->staff(User::factory()->create());
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post('/admin/leave', [
            'staff_profile_id' => $staff->id, 'leave_type' => 'annual',
            'start_date' => '2026-11-02', 'end_date' => '2026-11-06',
            'approver_type' => 'hr', 'approver_name' => 'HR', 'hr_notes' => 'Review this',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $leave = LeaveRequest::firstOrFail();
        $this->assertEquals(5, $leave->total_days);
        $this->get('/admin/leave?status=pending&search='.urlencode($staff->full_name))->assertOk();
        $this->get("/admin/leave/{$leave->id}")->assertOk();
        $this->get("/admin/leave/{$leave->id}/edit")->assertOk();
        $this->post('/admin/pension', [
            'staff_profile_id' => $staff->id, 'contribution_type' => 'percentage', 'status' => 'active',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $pension = \App\Models\PensionRecord::firstOrFail();
        $this->get("/admin/pension/{$pension->id}")->assertOk();
        $this->get("/admin/pension/{$pension->id}/edit")->assertOk();
    }

}
