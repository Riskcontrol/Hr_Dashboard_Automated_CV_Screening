<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppraisalSchedule;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class AppraisalScheduleController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year', date('Y'));
        $slots = AppraisalSchedule::with('appraiseeStaff')
            ->forYear($year)
            ->get()
            ->groupBy('day_number');

        $availableYears = AppraisalSchedule::availableYears();
        $staff          = StaffProfile::where('status','active')->orderBy('full_name')->get();

        return view('admin.appraisals.schedule', compact('slots','year','availableYears','staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year'               => 'required|integer|min:2020|max:2099',
            'day_number'         => 'required|integer|min:1|max:20',
            'session'            => 'required|in:morning,afternoon',
            'appraisee_name'     => 'required|string|max:255',
            'appraisee_staff_id' => 'nullable|exists:staff_profiles,id',
            'scheduled_date'     => 'required|date',
            'start_time'         => 'required',
            'end_time'           => 'required',
            'venue'              => 'nullable|string|max:255',
            'appraiser_name'     => 'required|string|max:255',
            'observer_names'     => 'nullable|string|max:255',
            'prepared_by'        => 'nullable|string|max:255',
            'prepared_date'      => 'nullable|date',
            'approved_by'        => 'nullable|string|max:255',
            'approved_date'      => 'nullable|date',
        ]);

        $validated['venue'] = $validated['venue'] ?? 'Teams';
        AppraisalSchedule::create($validated);

        return redirect()->route('admin.appraisal-schedule.index', ['year' => $validated['year']])
            ->with('success', 'Schedule slot added successfully!');
    }

    public function destroy(AppraisalSchedule $appraisalSchedule)
    {
        $year = $appraisalSchedule->year;
        $appraisalSchedule->delete();

        return redirect()->route('admin.appraisal-schedule.index', ['year' => $year])
            ->with('success', 'Schedule slot removed.');
    }
}