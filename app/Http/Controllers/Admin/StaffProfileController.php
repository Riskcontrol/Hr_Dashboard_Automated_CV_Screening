<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffProfile::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name',   'like', "%$s%")
                  ->orWhere('employee_id', 'like', "%$s%")
                  ->orWhere('email',       'like', "%$s%")
                  ->orWhere('job_title',   'like', "%$s%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff       = $query->latest()->paginate(15)->withQueryString();
        $departments = StaffProfile::distinct()->pluck('department')->filter()->sort()->values();

        $stats = [
            'total'      => StaffProfile::count(),
            'active'     => StaffProfile::where('status', 'active')->count(),
            'inactive'   => StaffProfile::where('status', '!=', 'active')->count(),
            'this_month' => StaffProfile::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.Staff.index', compact('staff', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $application    = null;
        if ($request->filled('from_application')) {
            $application = Application::find($request->from_application);
        }

        $nextId         = StaffProfile::generateEmployeeId();
        $availableUsers = \App\Models\User::whereDoesntHave('staffProfile')->orderBy('name')->get();

        return view('admin.Staff.create', compact('application', 'nextId', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'                      => 'required|string|max:255',
            'employee_id'                    => 'required|string|unique:staff_profiles,employee_id',
            'email'                          => 'required|email|unique:staff_profiles,email',
            'gender'                         => 'nullable|in:male,female,other',
            'date_of_birth'                  => 'nullable|date|before:today',
            'marital_status'                 => 'nullable|in:single,married,divorced,widowed',
            'nationality'                    => 'nullable|string|max:100',
            'phone_number'                   => 'nullable|string|max:20',
            'residential_address'            => 'nullable|string',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'job_title'                      => 'nullable|string|max:255',
            'department'                     => 'nullable|string|max:100',
            'location'                       => 'nullable|string|max:255',
            'employment_type'                => 'nullable|in:full_time,part_time,contract,intern',
            'date_of_hire'                   => 'nullable|date',
            'status'                         => 'nullable|in:active,inactive,suspended,terminated',
            'line_manager'                   => 'nullable|string|max:255',
            'department_head'                => 'nullable|string|max:255',
            'national_id'                    => 'nullable|string|max:100',
            'tax_id'                         => 'nullable|string|max:100',
            'pension_details'                => 'nullable|string|max:255',
            'salary'                         => 'nullable|numeric|min:0',
            'bank_name'                      => 'nullable|string|max:100',
            'bank_account_number'            => 'nullable|string|max:50',
            'academic_background'            => 'nullable|string',
            'certifications'                 => 'nullable|string',
            'professional_memberships'       => 'nullable|string',
            'previous_roles'                 => 'nullable|string',
            'promotion_history'              => 'nullable|string',
            'application_id'                 => 'nullable|exists:applications,id',
            'user_id'                        => 'nullable|exists:users,id|unique:staff_profiles,user_id',
            'profile_photo'                  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('staff_photos', 'public');
        }

        StaffProfile::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff profile for {$validated['full_name']} created successfully!");
    }

    public function show(StaffProfile $staff)
    {
        $staff->load('application');
        return view('admin.Staff.show', compact('staff'));
    }

    public function edit(StaffProfile $staff)
    {
        $availableUsers = \App\Models\User::where(function ($q) use ($staff) {
            $q->whereDoesntHave('staffProfile')
              ->orWhere('id', $staff->user_id);
        })->orderBy('name')->get();

        return view('admin.Staff.edit', compact('staff', 'availableUsers'));
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $validated = $request->validate([
            'full_name'                      => 'required|string|max:255',
            'employee_id'                    => 'required|string|unique:staff_profiles,employee_id,' . $staff->id,
            'email'                          => 'required|email|unique:staff_profiles,email,' . $staff->id,
            'gender'                         => 'nullable|in:male,female,other',
            'date_of_birth'                  => 'nullable|date|before:today',
            'marital_status'                 => 'nullable|in:single,married,divorced,widowed',
            'nationality'                    => 'nullable|string|max:100',
            'phone_number'                   => 'nullable|string|max:20',
            'residential_address'            => 'nullable|string',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'job_title'                      => 'nullable|string|max:255',
            'department'                     => 'nullable|string|max:100',
            'location'                       => 'nullable|string|max:255',
            'employment_type'                => 'nullable|in:full_time,part_time,contract,intern',
            'date_of_hire'                   => 'nullable|date',
            'status'                         => 'nullable|in:active,inactive,suspended,terminated',
            'line_manager'                   => 'nullable|string|max:255',
            'department_head'                => 'nullable|string|max:255',
            'national_id'                    => 'nullable|string|max:100',
            'tax_id'                         => 'nullable|string|max:100',
            'pension_details'                => 'nullable|string|max:255',
            'salary'                         => 'nullable|numeric|min:0',
            'bank_name'                      => 'nullable|string|max:100',
            'bank_account_number'            => 'nullable|string|max:50',
            'academic_background'            => 'nullable|string',
            'certifications'                 => 'nullable|string',
            'professional_memberships'       => 'nullable|string',
            'previous_roles'                 => 'nullable|string',
            'promotion_history'              => 'nullable|string',
            'user_id'                        => 'nullable|exists:users,id|unique:staff_profiles,user_id,' . $staff->id,
            'profile_photo'                  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($staff->profile_photo) {
                Storage::disk('public')->delete($staff->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('staff_photos', 'public');
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff profile updated successfully!');
    }

    public function destroy(StaffProfile $staff)
    {
        if ($staff->profile_photo) {
            Storage::disk('public')->delete($staff->profile_photo);
        }

        $name = $staff->full_name;
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', "$name has been removed from the system.");
    }
}