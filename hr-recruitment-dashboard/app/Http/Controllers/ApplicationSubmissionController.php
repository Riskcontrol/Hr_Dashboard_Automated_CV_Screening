<?php

// namespace App\Http\Controllers;

// use App\Models\Application;
// use App\Models\KeywordSet;
// use App\Services\FileUploadService;
// use App\Jobs\ProcessCVJob;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

// class ApplicationSubmissionController extends Controller
// {
//     protected $fileUploadService;

//     public function __construct(FileUploadService $fileUploadService = null)
//     {
//         // Make FileUploadService optional for now
//         $this->fileUploadService = $fileUploadService;
//     }

//     public function index()
//     {
//         try {
//             $keywordSets = KeywordSet::where('is_active', true)->get();
//         } catch (\Exception $e) {
//             $keywordSets = collect();
//         }
        
//         return view('application.form', compact('keywordSets'));
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'applicant_name' => 'required|string|max:255',
//             'applicant_email' => 'required|email|max:255',
//             'phone' => 'nullable|string|max:20',
//             'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
//             'job_position' => 'required|exists:keyword_sets,id'
//         ], [
//             'cv_file.mimes' => 'Only PDF, DOC, and DOCX files are allowed.',
//             'cv_file.max' => 'File size must not exceed 10MB.',
//             'job_position.required' => 'Please select a job position.',
//             'job_position.exists' => 'Selected job position is invalid.'
//         ]);

//         try {
//             // Simple file upload without service for now
//             $file = $request->file('cv_file');
//             $fileName = time() . '_' . $file->getClientOriginalName();
//             $filePath = $file->storeAs('cvs', $fileName, 'local');

//             // Create application record
//             $application = Application::create([
//                 'applicant_name' => $request->applicant_name,
//                 'applicant_email' => $request->applicant_email,
//                 'phone' => $request->phone,
//                 'cv_original_name' => $file->getClientOriginalName(),
//                 'cv_stored_path' => $filePath,
//                 'cv_file_size' => $file->getSize(),
//                 'qualification_status' => 'pending'
//             ]);

//             // Log the submission
//             Log::info('New CV application submitted', [
//                 'application_id' => $application->id,
//                 'applicant_email' => $application->applicant_email,
//                 'keyword_set_id' => $request->job_position
//             ]);

//             // For now, we'll skip the CV processing and just redirect to success
//             return redirect()->route('application.success', $application->id)
//                 ->with('success', 'Your application has been submitted successfully!');

//         } catch (\Exception $e) {
//             Log::error('Application submission failed', [
//                 'error' => $e->getMessage(),
//                 'applicant_email' => $request->applicant_email
//             ]);

//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Failed to submit application: ' . $e->getMessage());
//         }
//     }

//     public function success($applicationId)
//     {
//         try {
//             $application = Application::findOrFail($applicationId);
//             return view('application.success', compact('application'));
//         } catch (\Exception $e) {
//             return redirect()->route('application.form')
//                 ->with('error', 'Application not found.');
//         }
//     }

//     public function status($applicationId)
//     {
//         try {
//             $application = Application::with('keywordSet')->findOrFail($applicationId);
//             return view('application.status', compact('application'));
//         } catch (\Exception $e) {
//             return redirect()->route('application.form')
//                 ->with('error', 'Application not found.');
//         }
//     }
// }



namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\KeywordSet;
use App\Services\FileUploadService;
use App\Services\GitHubActionsProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessCVJob;

class ApplicationSubmissionController extends Controller
{
    protected $fileUploadService;
    protected $githubProcessor;

    public function __construct(FileUploadService $fileUploadService, GitHubActionsProcessorService $githubProcessor)
    {
        $this->fileUploadService = $fileUploadService;
        $this->githubProcessor = $githubProcessor;
    }

    public function index()
    {
        try {
            $keywordSets = KeywordSet::where('is_active', true)->get();
            return view('application.form', compact('keywordSets'));
        } catch (\Exception $e) {
            return view('application.form', ['keywordSets' => collect()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'job_position' => 'required|exists:keyword_sets,id'
        ], [
            'cv_file.mimes' => 'Only PDF, DOC, and DOCX files are allowed.',
            'cv_file.max' => 'File size must not exceed 10MB.',
            'job_position.required' => 'Please select a job position.',
            'job_position.exists' => 'Selected job position is invalid.'
        ]);

        try {
            // Upload the CV file
            $fileData = $this->fileUploadService->uploadCV(
                $request->file('cv_file'),
                $request->applicant_name
            );

            // Create application record
            $application = Application::create([
                'applicant_name' => $request->applicant_name,
                'applicant_email' => $request->applicant_email,
                'phone' => $request->phone,
                'cv_original_name' => $fileData['original_name'],
                'cv_stored_path' => $fileData['stored_path'],
                'cv_file_size' => $fileData['file_size'],
                'qualification_status' => 'pending',
                'keyword_set_id' => $request->job_position,
            ]);

            // Get keyword set
            $keywordSet = KeywordSet::find($request->job_position);

            // Dispatch job to process CV
            ProcessCVJob::dispatch($application, $keywordSet);

            Log::info('New CV application submitted and queued for processing', [
                'application_id' => $application->id,
                'applicant_email' => $application->applicant_email,
                'keyword_set_id' => $keywordSet->id
            ]);

            return redirect()->route('application.success', $application->id)
                ->with('success', 'Your application has been submitted successfully! Processing will begin shortly.');

        } catch (\Exception $e) {
            Log::error('Application submission failed', [
                'error' => $e->getMessage(),
                'applicant_email' => $request->applicant_email
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    private function processCV(Application $application, KeywordSet $keywordSet)
    {
        try {
            // For now, let's do a simple mock processing
            // In a real implementation, you'd extract text from the PDF/Word file
            
            // Mock extracted text (you can replace this with actual text extraction)
            $mockExtractedText = "I have experience with PHP, Laravel, MySQL, JavaScript, and web development. I also know HTML, CSS, and have worked on various projects.";
            
            // Simple keyword matching
            $keywords = $keywordSet->keywords;
            $extractedTextLower = strtolower($mockExtractedText);
            $foundKeywords = [];
            $missingKeywords = [];
            
            foreach ($keywords as $keyword) {
                if (str_contains($extractedTextLower, strtolower($keyword))) {
                    $foundKeywords[] = $keyword;
                } else {
                    $missingKeywords[] = $keyword;
                }
            }
            
            $totalKeywords = count($keywords);
            $foundCount = count($foundKeywords);
            $matchPercentage = $totalKeywords > 0 ? ($foundCount / $totalKeywords) * 100 : 0;
            $isQualified = $foundCount === $totalKeywords;
            
            // Update application with results
            $application->update([
                'extracted_text' => $mockExtractedText,
                'qualification_status' => $isQualified ? 'qualified' : 'not_qualified',
                'match_percentage' => round($matchPercentage, 2),
                'found_keywords' => $foundKeywords,
                'missing_keywords' => $missingKeywords,
                'processed_at' => now()
            ]);
            
            Log::info('CV processed successfully', [
                'application_id' => $application->id,
                'qualified' => $isQualified,
                'match_percentage' => $matchPercentage
            ]);
            
        } catch (\Exception $e) {
            Log::error('CV processing failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            
            $application->update(['qualification_status' => 'failed']);
        }
    }

    public function success($applicationId)
    {
        try {
            $application = Application::with('keywordSet')->findOrFail($applicationId);
            return view('application.success', compact('application'));
        } catch (\Exception $e) {
            return redirect()->route('application.form')
                ->with('error', 'Application not found.');
        }
    }

    public function status($applicationId)
    {
        try {
            $application = Application::with('keywordSet')->findOrFail($applicationId);
            return view('application.status', compact('application'));
        } catch (\Exception $e) {
            return redirect()->route('application.form')
                ->with('error', 'Application not found.');
        }
    }
}
