<?php

namespace App\Services;

use App\Models\Application;
use App\Models\KeywordSet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class GitHubActionsProcessorService
{
    private $githubToken;
    private $repositoryOwner;
    private $repositoryName;
    
    public function __construct()
    {
        $this->githubToken = config('services.github.token');
        $this->repositoryOwner = config('services.github.owner');
        $this->repositoryName = config('services.github.repo');
    }
    
    public function processApplication(Application $application, KeywordSet $keywordSet = null)
    {
        try {
            // Generate a temporary URL for the CV file
            $fileUrl = $this->generateTemporaryFileUrl($application->cv_stored_path);
            
            // Generate callback URL
            $callbackUrl = route('cv.processing.callback');
            
            // Generate authentication token for callback
            $authToken = $this->generateCallbackToken($application->id);
            
            // Trigger GitHub Actions workflow
            $workflowResult = $this->triggerGitHubWorkflow([
                'file_url' => $fileUrl,
                'application_id' => $application->id,
                'callback_url' => $callbackUrl,
                'auth_token' => $authToken
            ]);
            
            if ($workflowResult['success']) {
                // Mark application as processing
                $application->update([
                    'qualification_status' => 'processing',
                    'processing_started_at' => now()
                ]);
                
                Log::info('GitHub Actions workflow triggered successfully', [
                    'application_id' => $application->id,
                    'workflow_run_id' => $workflowResult['run_id']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'CV processing started via GitHub Actions',
                    'run_id' => $workflowResult['run_id']
                ];
            } else {
                throw new Exception('Failed to trigger GitHub Actions workflow');
            }
            
        } catch (Exception $e) {
            Log::error('GitHub Actions processing failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function generateTemporaryFileUrl($filePath)
    {
        // Generate a temporary signed URL (valid for 1 hour)
        $url = Storage::temporaryUrl($filePath, now()->addHour());
        
        // If using local storage, you might need to create a temporary route
        // For production, consider using S3 or similar with signed URLs
        return $url;
    }
    
    private function generateCallbackToken($applicationId)
    {
        // Generate a secure token for callback authentication
        return hash_hmac('sha256', $applicationId . now()->timestamp, config('app.key'));
    }
    
    private function triggerGitHubWorkflow($inputs)
    {
        try {
            $response = Http::withToken($this->githubToken)
                ->post("https://api.github.com/repos/{$this->repositoryOwner}/{$this->repositoryName}/actions/workflows/cv-processor.yml/dispatches", [
                    'ref' => 'main',
                    'inputs' => $inputs
                ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'run_id' => $response->header('Location') // GitHub returns run URL in Location header
                ];
            } else {
                throw new Exception('GitHub API returned: ' . $response->body());
            }
            
        } catch (Exception $e) {
            throw new Exception('Failed to trigger GitHub workflow: ' . $e->getMessage());
        }
    }
}
