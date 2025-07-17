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
        // Generate a temporary URL for the CV file
        $fileUrl = $this->generateTemporaryFileUrl($application->cv_stored_path);
        
        // Generate callback URL
        $callbackUrl = route('cv.processing.callback');
        
        // Generate authentication token for callback
        $authToken = $this->generateCallbackToken($application->id);
        
        // Trigger GitHub Actions workflow
        $workflowResult = $this->triggerGitHubWorkflow([
            'file_url' => $fileUrl,
            'application_id' => (string) $application->id,
            'callback_url' => $callbackUrl,
            'auth_token' => $authToken
        ]);
        
        // The triggerGitHubWorkflow method throws an exception on failure.
        // If we get here, it was successful.
        
        Log::info('GitHub Actions workflow triggered successfully', [
            'application_id' => $application->id,
            'workflow_run_id' => $workflowResult['run_id']
        ]);
        
        return [
            'success' => true,
            'message' => 'CV processing started via GitHub Actions',
            'run_id' => $workflowResult['run_id']
        ];
    }
    
    private function generateTemporaryFileUrl($filePath)
    {
        // Manually construct the URL to avoid issues with route() in CLI
        $appUrl = rtrim(config('app.url'), '/');
        $encodedPath = base64_encode($filePath);
        return "{$appUrl}/api/cv/file/{$encodedPath}";
    }
    
    private function generateCallbackToken($applicationId)
    {
        // Generate a secure token for callback authentication
        return hash_hmac('sha256', $applicationId, config('app.key'));
    }
    
    private function triggerGitHubWorkflow($inputs)
    {
        $url = "https://api.github.com/repos/{$this->repositoryOwner}/{$this->repositoryName}/actions/workflows/cv-processor.yml/dispatches";

        Log::info('Attempting to trigger GitHub workflow.', [
            'url' => $url,
            'token_present' => !empty($this->githubToken),
            'inputs' => $inputs
        ]);

        try {
            $response = Http::withToken($this->githubToken)
                ->post($url, [
                    'ref' => 'main',
                    'inputs' => $inputs
                ]);

            Log::info('Received response from GitHub API.', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'run_id' => $response->header('Location')
                ];
            } else {
                throw new Exception('GitHub API returned non-successful status: ' . $response->status());
            }
            
        } catch (Exception $e) {
            Log::error('Exception while triggering GitHub workflow.', [
                'message' => $e->getMessage()
            ]);
            throw new Exception('Failed to trigger GitHub workflow: ' . $e->getMessage());
        }
    }
}
