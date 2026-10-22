<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExitReport;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

/**
 * ExitReportController
 *
 * Manages the offboarding process when staff leave the company:
 * - Exit interview details and feedback
 * - 5-point clearance checklist (IT, Finance, HR, Line Manager, Admin)
 * - Final settlement tracking
 *
 * Routes in web.php:
 *   Route::resource('exit-reports', ExitReportController::class)
 */
class ExitReportController extends Controller
{
    /**
     * INDEX — List all exit reports with filters.
     * URL: GET /admin/exit-reports
     */
    public function index(Request $request)
    {
        $query = ExitReport::with('staffProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staffProfile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%");
            });
        }

        if ($request->filled('exit_type')) {
            $query->where('exit_type', $request->exit_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('clearance')) {
            if ($request->clearance === 'pending') {
                $query->pendingClearance();
            } elseif ($request->clearance === 'complete') {
                $query->where('it_clearance', true)
                      ->where('finance_clearance', true)
                      ->where('hr_clearance', true)
                      ->where('line_manager_clearance', true)
                      ->where('admin_clearance', true);
            }
        }

        $exitReports = $query->orderBy('last_working_day', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'              => ExitReport::count(),
            'in_progress'        => ExitReport::where('status', 'in_progress')->count(),
            'completed'          => ExitReport::where('status', 'completed')->count(),
            'pending_clearance'  => ExitReport::pendingClearance()->count(),
            'this_month'         => ExitReport::whereMonth('last_working_day', now()->month)
                                              ->whereYear('last_working_day', now()->year)
                                              ->count(),
        ];

        return view('admin.exit-reports.index', compact('exitReports', 'stats'));
    }

    /**
     * CREATE — Show form to start a new exit/offboarding process.
     * URL: GET /admin/exit-reports/create
     */
    public function create(Request $request)
    {
        $staff = StaffProfile::orderBy('full_name')->get();

        $selectedStaff = null;
        if ($request->filled('staff_id')) {
            $selectedStaff = StaffProfile::find($request->staff_id);
        }

        return view('admin.exit-reports.create', compact('staff', 'selectedStaff'));
    }

    /**
     * STORE — Save a new exit report.
     * URL: POST /admin/exit-reports
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id'          => 'required|exists:staff_profiles,id',
            'exit_type'                 => 'required|in:resignation,termination,end_of_contract,retirement,redundancy',
            'resignation_date'          => 'nullable|date',
            'last_working_day'          => 'required|date',
            'notice_period_days'        => 'nullable|integer|min:0',
            'exit_interview_date'       => 'nullable|date',
            'exit_interview_by'         => 'nullable|string|max:255',
            'reason_for_leaving'        => 'nullable|string|max:1000',
            'feedback_company'          => 'nullable|string|max:1000',
            'feedback_role'             => 'nullable|string|max:1000',
            'final_settlement_amount'   => 'nullable|numeric|min:0',
            'settlement_status'         => 'nullable|in:pending,processed,paid',
            'settlement_date'           => 'nullable|date',
            'status'                    => 'required|in:in_progress,completed,cancelled',
            'notes'                     => 'nullable|string|max:1000',
            'processed_by'              => 'nullable|string|max:255',
        ]);

        // Checkbox fields aren't sent in the request when unchecked,
        // so we manually set them to true/false based on presence
        $checkboxFields = [
            'exit_interview_conducted', 'would_recommend',
            'it_clearance', 'finance_clearance', 'hr_clearance',
            'line_manager_clearance', 'admin_clearance',
        ];
        foreach ($checkboxFields as $field) {
            $validated[$field] = $request->has($field);
        }

        $exitReport = ExitReport::create($validated);

        // Mark the staff profile as terminated if the exit is already completed
        if ($validated['status'] === 'completed') {
            $exitReport->staffProfile->update(['status' => 'terminated']);
        }

        return redirect()->route('admin.exit-reports.index')
            ->with('success', 'Exit report created successfully!');
    }

    /**
     * SHOW — View a single exit report in detail.
     * URL: GET /admin/exit-reports/{id}
     */
    public function show(ExitReport $exitReport)
    {
        $exitReport->load('staffProfile');
        return view('admin.exit-reports.show', compact('exitReport'));
    }

    /**
     * EDIT — Show edit form pre-filled with existing data.
     * URL: GET /admin/exit-reports/{id}/edit
     */
    public function edit(ExitReport $exitReport)
    {
        $staff = StaffProfile::orderBy('full_name')->get();
        return view('admin.exit-reports.edit', compact('exitReport', 'staff'));
    }

    /**
     * UPDATE — Save changes to an exit report.
     * Commonly used to tick off clearance checklist items over time.
     * URL: PUT /admin/exit-reports/{id}
     */
    public function update(Request $request, ExitReport $exitReport)
    {
        $validated = $request->validate([
            'staff_profile_id'          => 'required|exists:staff_profiles,id',
            'exit_type'                 => 'required|in:resignation,termination,end_of_contract,retirement,redundancy',
            'resignation_date'          => 'nullable|date',
            'last_working_day'          => 'required|date',
            'notice_period_days'        => 'nullable|integer|min:0',
            'exit_interview_date'       => 'nullable|date',
            'exit_interview_by'         => 'nullable|string|max:255',
            'reason_for_leaving'        => 'nullable|string|max:1000',
            'feedback_company'          => 'nullable|string|max:1000',
            'feedback_role'             => 'nullable|string|max:1000',
            'final_settlement_amount'   => 'nullable|numeric|min:0',
            'settlement_status'         => 'nullable|in:pending,processed,paid',
            'settlement_date'           => 'nullable|date',
            'status'                    => 'required|in:in_progress,completed,cancelled',
            'notes'                     => 'nullable|string|max:1000',
            'processed_by'              => 'nullable|string|max:255',
        ]);

        $checkboxFields = [
            'exit_interview_conducted', 'would_recommend',
            'it_clearance', 'finance_clearance', 'hr_clearance',
            'line_manager_clearance', 'admin_clearance',
        ];
        foreach ($checkboxFields as $field) {
            $validated[$field] = $request->has($field);
        }

        $exitReport->update($validated);

        if ($validated['status'] === 'completed') {
            $exitReport->staffProfile->update(['status' => 'terminated']);
        }

        return redirect()->route('admin.exit-reports.show', $exitReport)
            ->with('success', 'Exit report updated successfully!');
    }

    /**
     * DESTROY — Delete an exit report.
     * URL: DELETE /admin/exit-reports/{id}
     */
    public function destroy(ExitReport $exitReport)
    {
        $exitReport->delete();

        return redirect()->route('admin.exit-reports.index')
            ->with('success', 'Exit report deleted successfully.');
    }
}