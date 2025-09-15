<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\KeywordSet;
use App\Services\CVProcessorService;
use App\Jobs\ProcessCVJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with('keywordSet');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('qualification_status', $request->status);
        }

        if ($request->filled('keyword_set_id')) {
            $query->where('keyword_set_id', $request->keyword_set_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('applicant_name', 'LIKE', "%{$search}%")
                  ->orWhere('applicant_email', 'LIKE', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(15);
        $keywordSets = KeywordSet::active()->get();

        return view('admin.application.index', compact('applications', 'keywordSets'));
    }

    public function show(Application $application)
    {
        $application->load('keywordSet');
        return view('admin.application.show', compact('application'));
    }

    public function reprocess(Application $application, Request $request)
    {
        $request->validate([
            'keyword_set_id' => 'required|exists:keyword_sets,id'
        ]);

        $keywordSet = KeywordSet::findOrFail($request->keyword_set_id);

        // Queue the reprocessing job
        ProcessCVJob::dispatch($application, $keywordSet);

        return redirect()->back()
            ->with('success', 'CV reprocessing has been queued. Please refresh the page in a moment to see results.');
    }

    public function downloadCV(Application $application)
    {
        // Check if file exists using Laravel Storage
        if (!Storage::disk('local')->exists($application->cv_stored_path)) {
            return redirect()->back()->with('error', 'CV file not found!');
        }

        // Get the full file path
        $filePath = Storage::disk('local')->path($application->cv_stored_path);
        
        // Return file download response
        return response()->download($filePath, $application->cv_original_name);
    }

    public function destroy(Application $application)
    {
        // Delete the CV file
        Storage::disk('local')->delete($application->cv_stored_path);
        
        // Delete the application record
        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully!');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,reprocess',
            'applications' => 'required|array',
            'applications.*' => 'exists:applications,id',
            'keyword_set_id' => 'required_if:action,reprocess|exists:keyword_sets,id'
        ]);

        $applications = Application::whereIn('id', $request->applications)->get();

        if ($request->action === 'delete') {
            foreach ($applications as $application) {
                Storage::disk('local')->delete($application->cv_stored_path);
                $application->delete();
            }
            $message = count($applications) . ' applications deleted successfully!';
        
        } else { // reprocess
            $keywordSet = KeywordSet::findOrFail($request->keyword_set_id);
            
            foreach ($applications as $application) {
                ProcessCVJob::dispatch($application, $keywordSet);
            }
            $message = count($applications) . ' applications queued for reprocessing!';
        }

        return redirect()->route('admin.applications.index')
            ->with('success', $message);
    }

    public function exportQualified()
    {
        $applications = Application::qualified()
            ->with('keywordSet')
            ->get();

        $csvData = [];
        $csvData[] = ['Name', 'Email', 'Phone', 'Job Position', 'Match %', 'Submitted Date'];

        foreach ($applications as $app) {
            $csvData[] = [
                $app->applicant_name,
                $app->applicant_email,
                $app->phone ?? 'N/A',
                $app->keywordSet->job_title ?? 'N/A',
                $app->match_percentage . '%',
                $app->created_at->format('Y-m-d H:i:s')
            ];
        }

        $filename = 'qualified_applications_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}