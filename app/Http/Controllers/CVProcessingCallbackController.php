<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CVProcessingCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            // Log all incoming data for debugging
            Log::info('CV processing callback received', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

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
            $success = $request->input('success', false);
            $error = $request->input('error');
            
            Log::info('Callback payload analysis', [
                'application_id' => $applicationId,
                'has_extracted_text' => !empty($extractedText),
                'success_flag' => $success,
                'has_error' => !empty($error),
                'text_length' => $extractedText ? strlen($extractedText) : 0
            ]);
            
            if ($success && $extractedText && !$error) {
                // Processing was successful - now perform keyword matching
                Log::info('Processing successful, performing keyword matching', [
                    'application_id' => $applicationId,
                    'text_length' => strlen($extractedText)
                ]);
                
                // Perform keyword matching if we have a keyword set
                $qualification = 'not_qualified';
                $score = 0;
                $matchedKeywords = [];
                $missingKeywords = [];
                
                if ($application->keywordSet && $application->keywordSet->keywords) {
                    $keywords = $application->keywordSet->keywords;
                    if (is_string($keywords)) {
                        $keywords = json_decode($keywords, true) ?: [];
                    }
                    
                    // Convert text to lowercase for case-insensitive matching
                    $textLower = strtolower($extractedText);
                    
                    foreach ($keywords as $keyword) {
                        if (is_string($keyword) && stripos($textLower, strtolower($keyword)) !== false) {
                            $matchedKeywords[] = $keyword;
                        }
                    }
                    
                    $missingKeywords = array_diff($keywords, $matchedKeywords);
                    
                    // Calculate match percentage
                    $totalKeywords = count($keywords);
                    if ($totalKeywords > 0) {
                        $score = count($matchedKeywords) / $totalKeywords;
                        
                        // Qualify if match percentage is >= 50%
                        if ($score >= 0.5) {
                            $qualification = 'qualified';
                        }
                    }
                }
                
                // Update application with extracted text and results
                $application->update([
                    'extracted_text' => $extractedText,
                    'qualification_status' => $qualification,
                    'match_percentage' => round($score * 100, 2),
                    'found_keywords' => json_encode($matchedKeywords),
                    'missing_keywords' => json_encode($missingKeywords),
                    'processing_status' => 'completed',
                    'processed_at' => now()
                ]);
                
                Log::info('CV processing completed via GitHub Actions', [
                    'application_id' => $application->id,
                    'qualification_status' => $qualification,
                    'match_percentage' => round($score * 100, 2),
                    'matched_keywords_count' => count($matchedKeywords),
                    'missing_keywords_count' => count($missingKeywords)
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
