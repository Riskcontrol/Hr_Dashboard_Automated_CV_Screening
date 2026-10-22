<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

/**
 * AttendanceController
 *
 * Manages daily attendance and absence records for staff.
 * HR can:
 * - Log daily attendance (present, absent, late, half day, on leave)
 * - Record absence types and reasons
 * - View attendance history per staff member
 * - See summary stats (total absences, late arrivals, etc.)
 *
 * Routes in web.php:
 *   Route::resource('attendance', AttendanceController::class)
 */
class AttendanceController extends Controller
{
    /**
     * INDEX — List all attendance records with filters.
     * URL: GET /admin/attendance
     */
    public function index(Request $request)
    {
        $query = Attendance::with('staffProfile');

        // Search by staff name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staffProfile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by absence type
        if ($request->filled('absence_type')) {
            $query->where('absence_type', $request->absence_type);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        // Default: show most recent records first
        $records = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        // Summary stats for the top cards
        $stats = [
            'total'          => Attendance::count(),
            'absences'       => Attendance::absences()->count(),
            'late'           => Attendance::late()->count(),
            'unauthorised'   => Attendance::unauthorised()->count(),
            // This month's absences
            'this_month'     => Attendance::absences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
        ];

        return view('admin.attendance.index', compact('records', 'stats'));
    }

    /**
     * CREATE — Show form to log a new attendance record.
     * URL: GET /admin/attendance/create
     */
    public function create()
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.attendance.create', compact('staff'));
    }

    /**
     * STORE — Save a new attendance record.
     * URL: POST /admin/attendance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'date'             => 'required|date',
            'status'           => 'required|in:present,absent,late,half_day,on_leave,public_holiday',
            'absence_type'     => 'nullable|in:sick,unauthorised,personal,bereavement,maternity,other',
            'check_in_time'    => 'nullable|date_format:H:i',
            'check_out_time'   => 'nullable|date_format:H:i',
            'minutes_late'     => 'nullable|integer|min:0',
            'notes'            => 'nullable|string|max:1000',
            'recorded_by'      => 'nullable|string|max:255',
        ]);

        // Clear absence_type if status is not absent
        if ($validated['status'] !== 'absent') {
            $validated['absence_type'] = null;
        }

        // Prevent duplicate records for the same staff + date
        $exists = Attendance::where('staff_profile_id', $validated['staff_profile_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'date' => 'An attendance record already exists for this staff member on this date.'
            ])->withInput();
        }

        Attendance::create($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record saved successfully!');
    }

    /**
     * SHOW — View a single attendance record.
     * URL: GET /admin/attendance/{id}
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('staffProfile');

        // Load recent records for the same staff member for context
        $recentRecords = Attendance::where('staff_profile_id', $attendance->staff_profile_id)
            ->where('id', '!=', $attendance->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('admin.attendance.show', compact('attendance', 'recentRecords'));
    }

    /**
     * EDIT — Show edit form pre-filled with existing data.
     * URL: GET /admin/attendance/{id}/edit
     */
    public function edit(Attendance $attendance)
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.attendance.edit', compact('attendance', 'staff'));
    }

    /**
     * UPDATE — Save changes to an attendance record.
     * URL: PUT /admin/attendance/{id}
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'date'             => 'required|date',
            'status'           => 'required|in:present,absent,late,half_day,on_leave,public_holiday',
            'absence_type'     => 'nullable|in:sick,unauthorised,personal,bereavement,maternity,other',
            'check_in_time'    => 'nullable|date_format:H:i',
            'check_out_time'   => 'nullable|date_format:H:i',
            'minutes_late'     => 'nullable|integer|min:0',
            'notes'            => 'nullable|string|max:1000',
            'recorded_by'      => 'nullable|string|max:255',
        ]);

        if ($validated['status'] !== 'absent') {
            $validated['absence_type'] = null;
        }

        $attendance->update($validated);

        return redirect()->route('admin.attendance.show', $attendance)
            ->with('success', 'Attendance record updated successfully!');
    }

    /**
     * DESTROY — Delete an attendance record.
     * URL: DELETE /admin/attendance/{id}
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record deleted.');
    }

    /**
     * STAFF REPORT — View all attendance records for one staff member.
     * URL: GET /admin/attendance/staff/{staff}
     */
    public function staffReport(StaffProfile $staff, Request $request)
    {
        $query = Attendance::forStaff($staff->id);

        // Filter by month/year if provided
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        } else {
            // Default to current year
            $query->whereYear('date', now()->year);
        }

        $records = $query->orderBy('date', 'desc')->get();

        // Calculate summary for this staff member
        $summary = [
            'total_days'    => $records->count(),
            'present'       => $records->where('status', 'present')->count(),
            'absent'        => $records->where('status', 'absent')->count(),
            'late'          => $records->where('status', 'late')->count(),
            'on_leave'      => $records->where('status', 'on_leave')->count(),
            'unauthorised'  => $records->where('status', 'absent')
                                       ->where('absence_type', 'unauthorised')->count(),
        ];

        return view('admin.attendance.staff_report', compact('staff', 'records', 'summary'));
    }
}