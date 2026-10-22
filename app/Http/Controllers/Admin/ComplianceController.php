<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplianceRecord;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ComplianceController
 *
 * Handles certifications, licenses, and compliance documents for staff.
 * Tracks expiry dates and shows alerts for documents expiring soon
 * or already expired.
 *
 * Routes in web.php:
 *   Route::resource('compliance', ComplianceController::class)
 */
class ComplianceController extends Controller
{
    /**
     * INDEX — List all compliance records with filters and alerts.
     * URL: GET /admin/compliance
     */
    public function index(Request $request)
    {
        $query = ComplianceRecord::with('staffProfile');

        // Search by document name or staff name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'like', "%$search%")
                  ->orWhereHas('staffProfile', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%$search%");
                  });
            });
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by computed status (valid, expiring_soon, expired)
        // This needs special handling since computed_status is not a DB column
        if ($request->filled('status_filter')) {
            $status = $request->status_filter;
            if ($status === 'expired') {
                $query->expired();
            } elseif ($status === 'expiring_soon') {
                $query->expiringSoon();
            } elseif ($status === 'valid') {
                $query->where(function ($q) {
                    $q->whereNull('expiry_date')
                      ->orWhereDate('expiry_date', '>', now()->addDays(30));
                })->where('status', '!=', 'renewal_in_progress');
            } elseif ($status === 'renewal_in_progress') {
                $query->where('status', 'renewal_in_progress');
            }
        }

        // Order by expiry date — soonest expiring first, nulls last
        $records = $query->orderByRaw('expiry_date IS NULL, expiry_date ASC')
            ->paginate(15)
            ->withQueryString();

        // Summary stats for the top cards
        $stats = [
            'total'          => ComplianceRecord::count(),
            'expiring_soon'  => ComplianceRecord::expiringSoon()->count(),
            'expired'        => ComplianceRecord::expired()->count(),
            'valid'          => ComplianceRecord::count()
                - ComplianceRecord::expiringSoon()->count()
                - ComplianceRecord::expired()->count(),
        ];

        return view('admin.compliance.index', compact('records', 'stats'));
    }

    /**
     * CREATE — Show form to add a new compliance record.
     * URL: GET /admin/compliance/create
     */
    public function create()
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.compliance.create', compact('staff'));
    }

    /**
     * STORE — Save a new compliance record.
     * URL: POST /admin/compliance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'document_type'    => 'required|in:certification,license,insurance,contract,id_document,medical,background_check',
            'document_name'    => 'required|string|max:255',
            'document_number'  => 'nullable|string|max:100',
            'issuing_body'     => 'nullable|string|max:255',
            'issue_date'       => 'nullable|date',
            'expiry_date'      => 'nullable|date',
            'status'           => 'required|in:valid,renewal_in_progress',
            'notes'            => 'nullable|string|max:1000',
            'document_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Upload the document file if provided
        if ($request->hasFile('document_file')) {
            $validated['document_file'] = $request->file('document_file')
                ->store('compliance_documents', 'public');
        }

        ComplianceRecord::create($validated);

        return redirect()->route('admin.compliance.index')
            ->with('success', 'Compliance record added successfully!');
    }

    /**
     * SHOW — View a single compliance record in detail.
     * URL: GET /admin/compliance/{id}
     */
    public function show(ComplianceRecord $compliance)
    {
        $compliance->load('staffProfile');

        // Load other compliance records for the same staff member
        $otherRecords = ComplianceRecord::where('staff_profile_id', $compliance->staff_profile_id)
            ->where('id', '!=', $compliance->id)
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
            ->get();

        return view('admin.compliance.show', compact('compliance', 'otherRecords'));
    }

    /**
     * EDIT — Show edit form pre-filled with existing record.
     * URL: GET /admin/compliance/{id}/edit
     */
    public function edit(ComplianceRecord $compliance)
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.compliance.edit', compact('compliance', 'staff'));
    }

    /**
     * UPDATE — Save changes to a compliance record.
     * URL: PUT /admin/compliance/{id}
     */
    public function update(Request $request, ComplianceRecord $compliance)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'document_type'    => 'required|in:certification,license,insurance,contract,id_document,medical,background_check',
            'document_name'    => 'required|string|max:255',
            'document_number'  => 'nullable|string|max:100',
            'issuing_body'     => 'nullable|string|max:255',
            'issue_date'       => 'nullable|date',
            'expiry_date'      => 'nullable|date',
            'status'           => 'required|in:valid,renewal_in_progress',
            'notes'            => 'nullable|string|max:1000',
            'document_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // If a new file was uploaded, delete the old one and save the new one
        if ($request->hasFile('document_file')) {
            if ($compliance->document_file) {
                Storage::disk('public')->delete($compliance->document_file);
            }
            $validated['document_file'] = $request->file('document_file')
                ->store('compliance_documents', 'public');
        }

        $compliance->update($validated);

        return redirect()->route('admin.compliance.show', $compliance)
            ->with('success', 'Compliance record updated successfully!');
    }

    /**
     * DESTROY — Delete a compliance record.
     * URL: DELETE /admin/compliance/{id}
     */
    public function destroy(ComplianceRecord $compliance)
    {
        // Delete the uploaded file if it exists
        if ($compliance->document_file) {
            Storage::disk('public')->delete($compliance->document_file);
        }

        $name = $compliance->document_name;
        $compliance->delete();

        return redirect()->route('admin.compliance.index')
            ->with('success', "'{$name}' record deleted successfully.");
    }
}