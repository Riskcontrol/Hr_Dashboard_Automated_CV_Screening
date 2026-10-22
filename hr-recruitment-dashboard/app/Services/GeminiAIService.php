<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiAIService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function evaluateCV($extractedText, $jobTitle, $keywords, $applicantName = null)
    {
        try {
            $prompt = $this->buildEvaluationPrompt($extractedText, $jobTitle, $keywords, $applicantName);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $this->parseAIResponse($data['candidates'][0]['content']['parts'][0]['text']);
                }
            }

            Log::error('Gemini API error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('AI evaluation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function buildEvaluationPrompt($extractedText, $jobTitle, $keywords, $applicantName)
    {
        $keywordsList = is_array($keywords) ? implode(', ', $keywords) : $keywords;
        
        return "You are an expert HR recruiter evaluating a CV for the position of '{$jobTitle}'. 

**Job Requirements:**
- Position: {$jobTitle}
- Required Skills/Keywords: {$keywordsList}

**CV Content:**
{$extractedText}

Please provide a comprehensive evaluation in the following JSON format:

{
    \"overall_evaluation\": \"A detailed paragraph evaluating the candidate's overall suitability for the position\",
    \"score\": 85,
    \"strengths\": [
        \"List of candidate's key strengths\",
        \"Relevant experience\",
        \"Technical skills\"
    ],
    \"weaknesses\": [
        \"Areas for improvement\",
        \"Missing skills or experience\",
        \"Gaps in requirements\"
    ],
    \"recommendation\": \"RECOMMEND/CONSIDER/NOT_RECOMMEND with brief reasoning\",
    \"key_highlights\": [
        \"Most impressive achievements\",
        \"Standout qualifications\"
    ],
    \"concerns\": [
        \"Any red flags or concerns\",
        \"Areas that need clarification\"
    ]
}

**Evaluation Criteria:**
1. Relevance of experience to the job position
2. Technical skills match with requirements
3. Education and certifications
4. Career progression and achievements
5. Communication skills (as evidenced in CV)
6. Overall professional presentation

Provide only the JSON response, no additional text.";
    }

    private function parseAIResponse($response)
    {
        try {
            // Clean the response - remove any markdown code blocks or extra text
            $response = trim($response);
            $response = preg_replace('/^```json\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
            
            $parsed = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON parsing error in AI response', [
                    'error' => json_last_error_msg(),
                    'response' => $response
                ]);
                return null;
            }

            return [
                'evaluation' => $parsed['overall_evaluation'] ?? 'No evaluation provided',
                'score' => $parsed['score'] ?? 0,
                'strengths' => $parsed['strengths'] ?? [],
                'weaknesses' => $parsed['weaknesses'] ?? [],
                'recommendation' => $parsed['recommendation'] ?? 'NOT_RECOMMEND',
                'key_highlights' => $parsed['key_highlights'] ?? [],
                'concerns' => $parsed['concerns'] ?? []
            ];

        } catch (Exception $e) {
            Log::error('Error parsing AI response', [
                'error' => $e->getMessage(),
                'response' => $response
            ]);
            return null;
        }
    }
}
