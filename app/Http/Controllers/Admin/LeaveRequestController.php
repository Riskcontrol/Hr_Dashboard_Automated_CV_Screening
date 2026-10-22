<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public const DEFAULT_ALLOWANCES = [
        'annual'    => 21,
        'sick'      => 12,
        'casual'    => 5,
        'maternity' => 90,
        'paternity' => 5,
        'unpaid'    => null,
    ];

    public function index(Request $request)
    {
        $query = LeaveRequest::with('staffProfile');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staffProfile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('from')) {
            $query->where('start_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('end_date', '<=', $request->to);
        }
        if ($request->filled('department')) {
            $query->whereHas('staffProfile', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $leaves      = $query->latest()->paginate(15)->withQueryString();
        $stats       = [
            'total'    => LeaveRequest::count(),
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];
        $departments = StaffProfile::distinct()->pluck('department')->filter()->sort()->values();
        $allowances  = self::DEFAULT_ALLOWANCES;

        return view('admin.Leave.index', compact('leaves', 'stats', 'departments', 'allowances'));
    }

    public function create()
    {
        $staff      = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        $allowances = self::DEFAULT_ALLOWANCES;
        return view('admin.Leave.create', compact('staff', 'allowances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'leave_type'       => 'required|in:annual,sick,casual,maternity,paternity,unpaid',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'reason'           => 'nullable|string|max:1000',
            'approver_type'    => 'required|in:line_manager,hr',
            'approver_name'    => 'required|string|max:255',
            'hr_notes'         => 'nullable|string',
            'staff_section'    => 'nullable|string|max:255',
        ]);

        $validated['total_days']   = LeaveRequest::calculateWorkingDays($validated['start_date'], $validated['end_date']);
        $validated['status']       = 'pending';
        $validated['submitted_by'] = 'hr';

        LeaveRequest::create($validated);

        return redirect()->route('admin.leave.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leave)
    {
        $leave->load('staffProfile');

        $usedThisYear = LeaveRequest::where('staff_profile_id', $leave->staff_profile_id)
            ->where('leave_type', $leave->leave_type)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $allowance     = self::DEFAULT_ALLOWANCES[$leave->leave_type] ?? null;
        $remainingDays = $allowance ? max(0, $allowance - $usedThisYear) : null;

        return view('admin.Leave.show', compact('leave', 'usedThisYear', 'allowance', 'remainingDays'));
    }

    public function edit(LeaveRequest $leave)
    {
        if ($leave->status !== 'pending') {
            return redirect()->route('admin.leave.show', $leave)
                ->with('error', 'Only pending requests can be edited.');
        }
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.Leave.edit', compact('leave', 'staff'));
    }

    public function update(Request $request, LeaveRequest $leave)
    {
        abort_unless($leave->status === 'pending', 403, 'Only pending requests can be edited.');
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'leave_type'       => 'required|in:annual,sick,casual,maternity,paternity,unpaid',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'reason'           => 'nullable|string|max:1000',
            'approver_type'    => 'required|in:line_manager,hr',
            'approver_name'    => 'required|string|max:255',
            'hr_notes'         => 'nullable|string',
            'staff_section'    => 'nullable|string|max:255',
        ]);

        $validated['total_days'] = LeaveRequest::calculateWorkingDays($validated['start_date'], $validated['end_date']);
        $leave->update($validated);

        return redirect()->route('admin.leave.show', $leave)->with('success', 'Leave request updated.');
    }

    public function destroy(LeaveRequest $leave)
    {
        $leave->delete();
        return redirect()->route('admin.leave.index')->with('success', 'Leave request deleted.');
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        abort_unless($leave->status === 'pending', 403, 'Only pending requests can be reviewed.');
        abort_unless(auth()->user()->hasAdminPrivileges(), 403, 'Only HR can approve leave requests.');
        $request->validate(['approved_by' => 'required|string|max:255']);
        $leave->update([
            'status'      => 'approved',
            'approved_by' => $request->approved_by,
            'approved_at' => now(),
        ]);
        return redirect()->route('admin.leave.show', $leave)->with('success', 'Leave request approved.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        abort_unless($leave->status === 'pending', 403, 'Only pending requests can be reviewed.');
        abort_unless(auth()->user()->hasAdminPrivileges(), 403, 'Only HR can reject leave requests.');
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'approved_by'      => 'required|string|max:255',
        ]);
        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => $request->approved_by,
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return redirect()->route('admin.leave.show', $leave)->with('success', 'Leave request rejected.');
    }
}