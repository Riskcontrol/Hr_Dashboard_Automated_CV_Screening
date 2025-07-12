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
        try {
            // Validate the callback
            $authToken = $request->bearerToken();
            $applicationId = $request->input('application_id');
            
            if (!$this->validateCallbackToken($authToken, $applicationId)) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
            
            $application = Application::with('keywordSet')->findOrFail($applicationId);
            
            if ($request->input('success')) {
                // Processing was successful
                $extractedText = $request->input('extracted_text');
                
                // Update application with extracted text
                $application->update(['extracted_text' => $extractedText]);
                
                // Perform keyword matching
                if ($application->keywordSet) {
                    $matchResult = $this->cvProcessorService->matchKeywords($extractedText, $application->keywordSet->keywords);
                    
                    $application->update([
                        'qualification_status' => $matchResult['qualified'] ? 'qualified' : 'not_qualified',
                        'match_percentage' => $matchResult['match_percentage'],
                        'found_keywords' => $matchResult['found_keywords'],
                        'missing_keywords' => $matchResult['missing_keywords'],
                        'processed_at' => now(),
                        'processing_status' => 'completed'
                    ]);
                    
                    Log::info('CV processing completed via GitHub Actions', [
                        'application_id' => $application->id,
                        'qualified' => $matchResult['qualified'],
                        'match_percentage' => $matchResult['match_percentage']
                    ]);
                }
            } else {
                // Processing failed
                $error = $request->input('error', 'Unknown error');
                
                $application->update([
                    'qualification_status' => 'failed',
                    'processing_error' => $error,
                    'processing_status' => 'failed'
                ]);
                
                Log::error('CV processing failed via GitHub Actions', [
                    'application_id' => $application->id,
                    'error' => $error
                ]);
            }
            
            return response()->json(['success' => true]);
            
        } catch (Exception $e) {
            Log::error('Callback processing error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Callback processing failed'], 500);
        }
    }
    
    private function validateCallbackToken($token, $applicationId)
    {
        // Validate the callback token (implement your validation logic)
        $expectedToken = hash_hmac('sha256', $applicationId . now()->timestamp, config('app.key'));
        
        // In production, you might want to use a more sophisticated validation
        // that accounts for time windows, etc.
        return hash_equals($expectedToken, $token);
    }
}
