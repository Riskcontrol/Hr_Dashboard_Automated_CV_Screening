<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class StaffPortalController extends Controller
{
    private function staffProfileOrFail()
    {
        $staffProfile = auth()->user()->staffProfile;
        abort_if(!$staffProfile, 403, 'Your account is not yet linked to a staff record. Please contact HR.');
        return $staffProfile;
    }

    public function dashboard()
    {
        $staffProfile = $this->staffProfileOrFail();
        $leaveStats = [
            'pending'  => $staffProfile->leaveRequests()->where('status', 'pending')->count(),
            'approved' => $staffProfile->leaveRequests()->where('status', 'approved')->count(),
            'rejected' => $staffProfile->leaveRequests()->where('status', 'rejected')->count(),
        ];
        $latestAppraisal = $staffProfile->appraisals()->latest('due_date')->first();
        return view('my.dashboard', compact('staffProfile', 'leaveStats', 'latestAppraisal'));
    }

    public function leaveIndex()
    {
        $staffProfile = $this->staffProfileOrFail();
        $leaves = $staffProfile->leaveRequests()->latest()->paginate(10);
        $stats = [
            'total'    => $staffProfile->leaveRequests()->count(),
            'pending'  => $staffProfile->leaveRequests()->where('status', 'pending')->count(),
            'approved' => $staffProfile->leaveRequests()->where('status', 'approved')->count(),
            'rejected' => $staffProfile->leaveRequests()->where('status', 'rejected')->count(),
        ];
        return view('my.leave.index', compact('leaves', 'stats'));
    }

    public function leaveCreate()
    {
        $this->staffProfileOrFail();
        return view('my.leave.create');
    }

    public function leaveStore(Request $request)
    {
        $staffProfile = $this->staffProfileOrFail();
        $validated = $request->validate([
            'leave_type'    => 'required|in:annual,sick,casual,maternity,paternity,unpaid',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string|max:1000',
            'approver_type' => 'required|in:line_manager,hr',
            'approver_name' => 'required|string|max:255',
        ]);
        $validated['staff_profile_id'] = $staffProfile->id;
        $validated['total_days']       = LeaveRequest::calculateWorkingDays($validated['start_date'], $validated['end_date']);
        $validated['status']           = 'pending';
        $validated['submitted_by']     = 'staff';
        LeaveRequest::create($validated);
        return redirect()->route('my.leave.index')->with('success', 'Leave request submitted and awaiting approval.');
    }

    public function leaveShow(LeaveRequest $leave)
    {
        $staffProfile = $this->staffProfileOrFail();
        abort_unless($leave->staff_profile_id === $staffProfile->id, 403);
        return view('my.leave.show', compact('leave'));
    }

    public function appraisalIndex()
    {
        $staffProfile = $this->staffProfileOrFail();
        $appraisals   = $staffProfile->appraisals()->latest('due_date')->paginate(10);
        return view('my.appraisals.index', compact('appraisals'));
    }

    public function appraisalShow(\App\Models\Appraisal $appraisal)
    {
        $staffProfile = $this->staffProfileOrFail();
        abort_unless($appraisal->staff_profile_id === $staffProfile->id, 403);
        return view('my.appraisals.show', compact('appraisal'));
    }

    public function appraisalFill(\App\Models\Appraisal $appraisal)
    {
        $staffProfile = $this->staffProfileOrFail();
        abort_unless($appraisal->staff_profile_id === $staffProfile->id, 403);
        abort_unless($appraisal->sent_to_employee_at, 403, 'This form has not been sent to you yet.');
        return view('admin.appraisals.fill', compact('appraisal'));
    }

    public function appraisalSave(Request $request, \App\Models\Appraisal $appraisal)
    {
        $staffProfile = $this->staffProfileOrFail();
        abort_unless($appraisal->staff_profile_id === $staffProfile->id, 403);
        abort_unless($appraisal->sent_to_employee_at, 403, 'This form has not been sent to you yet.');
        $validated = $request->validate([
            'self_mission_statement'    => 'nullable|string',
            'self_duties_understanding' => 'nullable|string',
            'self_job_achievements'     => 'nullable|string',
            'self_other_achievements'   => 'nullable|string',
            'self_likes_dislikes'       => 'nullable|string',
            'self_most_difficult'       => 'nullable|string',
            'self_improvement_actions'  => 'nullable|string',
            'employee_comments'         => 'nullable|string',
        ]);
        $appraisal->recordEdit($staffProfile->full_name . ' (employee)');
        $appraisal->fill($validated);
        $appraisal->save();
        return redirect()->route('my.appraisals.show', $appraisal)
            ->with('success', 'Self-evaluation saved. ' . now()->format('M d, Y g:i A'));
    }

    public function employeeRules()
    {
        $this->staffProfileOrFail();
        $handbook      = \App\Models\EmployeeRule::where('type', 'handbook')->latest()->get();
        $codeOfConduct = \App\Models\EmployeeRule::where('type', 'code_of_conduct')->latest()->get();
        return view('my.employee-rules', compact('handbook', 'codeOfConduct'));
    }

    public function employeeRulesDownload(\App\Models\EmployeeRule $employeeRule)
    {
        $this->staffProfileOrFail();
        return response()->file(storage_path('app/public/' . $employeeRule->file_path));
    }
}