<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\KeywordSet;
use App\Services\FileUploadService;
use App\Jobs\ProcessCVJob;
use Illuminate\Http\Request;

class ApplicationApiController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getJobPositions()
    {
        $positions = KeywordSet::active()
            ->select('id', 'job_title', 'description')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $positions
        ]);
    }

    public function submitApplication(ApplicationRequest $request)
    {
        try {
            $fileData = $this->fileUploadService->uploadCV(
                $request->file('cv_file'),
                $request->applicant_name
            );

            $application = Application::create([
                'applicant_name' => $request->applicant_name,
                'applicant_email' => $request->applicant_email,
                'phone' => $request->phone,
                'cv_original_name' => $fileData['original_name'],
                'cv_stored_path' => $fileData['stored_path'],
                'cv_file_size' => $fileData['file_size'],
                'qualification_status' => 'pending'
            ]);

            ProcessCVJob::dispatch($application, $request->job_position);

            return response()->json([
                'success' => true,
                'data' => [
                    'application_id' => $application->id,
                    'message' => 'Application submitted successfully'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getApplicationStatus($id)
    {
        $application = Application::with('keywordSet')->find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $application->id,
                'applicant_name' => $application->applicant_name,
                'job_position' => $application->keywordSet->job_title ?? null,
                'status' => $application->qualification_status,
                'match_percentage' => $application->match_percentage,
                'found_keywords' => $application->found_keywords,
                'missing_keywords' => $application->missing_keywords,
                'submitted_at' => $application->created_at,
                'processed_at' => $application->processed_at
            ]
        ]);
    }
}