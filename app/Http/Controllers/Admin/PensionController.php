<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PensionRecord;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class PensionController extends Controller
{
    public function index(Request $request)
    {
        $query = PensionRecord::with('staffProfile');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('staffProfile', function ($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('employee_id', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => PensionRecord::count(),
            'active'    => PensionRecord::where('status', 'active')->count(),
            'suspended' => PensionRecord::where('status', 'suspended')->count(),
            'no_record' => StaffProfile::where('status', 'active')
                               ->doesntHave('pensionRecord')->count(),
        ];

        $staffWithoutPension = StaffProfile::where('status', 'active')
            ->doesntHave('pensionRecord')
            ->orderBy('full_name')
            ->get();

        return view('admin.pension.index', compact('records', 'stats', 'staffWithoutPension'));
    }

    public function create()
    {
        $staffWithoutPension = StaffProfile::where('status', 'active')
            ->doesntHave('pensionRecord')->orderBy('full_name')->get();

        return view('admin.pension.create', compact('staffWithoutPension'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id'      => 'required|exists:staff_profiles,id|unique:pension_records,staff_profile_id',
            'pension_provider'      => 'nullable|string|max:255',
            'pension_pin'           => 'nullable|string|max:100',
            'rsa_number'            => 'nullable|string|max:100',
            'employee_contribution' => 'nullable|numeric|min:0',
            'employer_contribution' => 'nullable|numeric|min:0',
            'contribution_type'     => 'required|in:percentage,fixed',
            'enrollment_date'       => 'nullable|date',
            'status'                => 'required|in:active,suspended,exited',
            'notes'                 => 'nullable|string',
        ]);

        PensionRecord::create($validated);

        return redirect()->route('admin.pension.index')
            ->with('success', 'Pension record created successfully.');
    }

    public function show(PensionRecord $pension)
    {
        $pension->load('staffProfile');
        return view('admin.pension.show', compact('pension'));
    }

    public function edit(PensionRecord $pension)
    {
        $pension->load('staffProfile');
        return view('admin.pension.edit', compact('pension'));
    }

    public function update(Request $request, PensionRecord $pension)
    {
        $validated = $request->validate([
            'pension_provider'      => 'nullable|string|max:255',
            'pension_pin'           => 'nullable|string|max:100',
            'rsa_number'            => 'nullable|string|max:100',
            'employee_contribution' => 'nullable|numeric|min:0',
            'employer_contribution' => 'nullable|numeric|min:0',
            'contribution_type'     => 'required|in:percentage,fixed',
            'enrollment_date'       => 'nullable|date',
            'status'                => 'required|in:active,suspended,exited',
            'notes'                 => 'nullable|string',
        ]);

        $pension->update($validated);

        return redirect()->route('admin.pension.show', $pension)
            ->with('success', 'Pension record updated.');
    }

    public function destroy(PensionRecord $pension)
    {
        $name = $pension->staffProfile->full_name;
        $pension->delete();

        return redirect()->route('admin.pension.index')
            ->with('success', "Pension record for {$name} deleted.");
    }
}