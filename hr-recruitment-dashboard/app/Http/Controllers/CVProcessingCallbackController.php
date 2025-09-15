<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\CVProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CVProcessingCallbackController extends Controller
{
    protected $cvProcessorService;

    public function __construct(CVProcessorService $cvProcessorService)
    {
        $this->cvProcessorService = $cvProcessorService;
    }

    public function handleCallback(Request $request)
    {
        // Log all incoming data for debugging
        Log::info('CV processing callback received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        try {
            $applicationId = $request->input('application_id');
            $authToken = $request->bearerToken() ?? $request->input('auth_token');
            
            if (!$applicationId) {
                Log::error('Missing application_id in callback');
                return response()->json(['error' => 'Missing application_id'], 400);
            }
            
            if (!$authToken) {
                Log::error('Missing auth token in callback');
                return response()->json(['error' => 'Missing auth token'], 401);
            }
            
            if (!$this->validateCallbackToken($authToken, $applicationId)) {
                Log::error('Invalid callback token', [
                    'application_id' => $applicationId,
                    'provided_token' => $authToken
                ]);
                return response()->json(['error' => 'Invalid token'], 401);
            }
            
            $application = Application::with('keywordSet')->find($applicationId);
            if (!$application) {
                Log::error('Application not found', ['application_id' => $applicationId]);
                return response()->json(['error' => 'Application not found'], 404);
            }
            
            // Check if we have extracted text (indicates successful processing)
            $extractedText = $request->input('extracted_text');
            $qualification = $request->input('qualification');
            $score = $request->input('score');
            $matchedKeywords = $request->input('matched_keywords', []);
            $processingLog = $request->input('processing_log');
            $error = $request->input('error');
            
            if ($extractedText && !$error) {
                // Processing was successful
                Log::info('Processing successful, updating application', [
                    'application_id' => $applicationId,
                    'text_length' => strlen($extractedText),
                    'qualification' => $qualification,
                    'score' => $score
                ]);
                
                // Update application with extracted text and results
                $application->update([
                    'extracted_text' => $extractedText,
                    'qualification_status' => $qualification === 'qualified' ? 'qualified' : 'not_qualified',
                    'match_percentage' => $score ? ($score * 100) : null, // Convert score to percentage
                    'found_keywords' => is_array($matchedKeywords) ? json_encode($matchedKeywords) : $matchedKeywords,
                    'processing_status' => 'completed',
                    'processed_at' => now()
                ]);
                
                Log::info('CV processing completed via GitHub Actions', [
                    'application_id' => $application->id,
                    'qualification_status' => $qualification,
                    'score' => $score,
                    'matched_keywords_count' => is_array($matchedKeywords) ? count($matchedKeywords) : 0
                ]);
                
            } else {
                // Processing failed
                $errorMessage = $error ?: 'No extracted text received';
                
                $application->update([
                    'qualification_status' => 'failed',
                    'processing_error' => $errorMessage,
                    'processing_status' => 'failed'
                ]);
                
                Log::error('CV processing failed via GitHub Actions', [
                    'application_id' => $application->id,
                    'error' => $errorMessage
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully',
                'application_id' => $applicationId
            ]);
            
        } catch (Exception $e) {
            Log::error('Callback processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Callback processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function validateCallbackToken($token, $applicationId)
    {
        // Validate the callback token
        $expectedToken = hash_hmac('sha256', $applicationId, config('app.key'));
        return hash_equals($expectedToken, $token);
    }
}
