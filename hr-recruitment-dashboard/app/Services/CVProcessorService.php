<?php

namespace App\Services;

use App\Models\Application;
use App\Models\KeywordSet;
use App\Models\ProcessingLog;
use Spatie\PdfToText\Pdf;
use PhpOffice\PhpWord\IOFactory;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Mail\ApplicationProcessed;
use Illuminate\Support\Facades\Mail;
class CVProcessorService
{
    public function processApplication(Application $application, KeywordSet $keywordSet = null)
    {
        $startTime = microtime(true);
        
        try {
            // Log processing start
            $log = ProcessingLog::create([
                'application_id' => $application->id,
                'action' => 'text_extraction',
                'status' => 'in_progress'
            ]);

            // Extract text from CV
            $extractedText = $this->extractTextFromFile($application->cv_stored_path);
            
            if (empty($extractedText)) {
                throw new Exception('No text could be extracted from the CV');
            }

            // Update application with extracted text
            $application->update(['extracted_text' => $extractedText]);

            // If keyword set is provided, perform matching
            if ($keywordSet) {
                $matchResult = $this->matchKeywords($extractedText, $keywordSet->keywords);
                
                $qualificationStatus = $this->determineQualificationStatus($matchResult['found_count'], $matchResult['total_keywords']);
                
                $application->update([
                    'keyword_set_id' => $keywordSet->id,
                    'qualification_status' => $qualificationStatus,
                    'match_percentage' => $matchResult['match_percentage'],
                    'found_keywords' => $matchResult['found_keywords'],
                    'missing_keywords' => $matchResult['missing_keywords'],
                    'processed_at' => now()
                ]);
                try {
                    Mail::to($application->applicant_email)->send(new ApplicationProcessed($application));
                } catch (\Exception $e) {
                    Log::warning('Failed to send notification email', [
                        'application_id' => $application->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log success
            $processingTime = (microtime(true) - $startTime) * 1000;
            $log->update([
                'status' => 'success',
                'processing_time_ms' => round($processingTime)
            ]);

            return [
                'success' => true,
                'extracted_text' => $extractedText,
                'match_result' => $matchResult ?? null
            ];

        } catch (Exception $e) {
            // Log error
            ProcessingLog::create([
                'application_id' => $application->id,
                'action' => 'processing_error',
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $application->update(['qualification_status' => 'failed']);

            Log::error('CV Processing failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'file_path' => $application->cv_stored_path
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function extractTextFromFile($filePath)
    {
        $fullPath = storage_path('app/' . $filePath);
        
        if (!file_exists($fullPath)) {
            throw new Exception('File not found: ' . $filePath);
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'pdf':
                return $this->extractFromPDF($fullPath);
            case 'docx':
            case 'doc':
                return $this->extractFromWord($fullPath);
            default:
                throw new Exception('Unsupported file format: ' . $extension);
        }
    }

    private function extractFromPDF($filePath)
    {
        try {
            $text = Pdf::getText($filePath);
            return trim($text);
        } catch (Exception $e) {
            throw new Exception('Failed to extract text from PDF: ' . $e->getMessage());
        }
    }

    private function extractFromWord($filePath)
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun || $element instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            return trim($text);
        } catch (Exception $e) {
            throw new Exception('Failed to extract text from Word document: ' . $e->getMessage());
        }
    }

    public function matchKeywords($extractedText, $keywords)
    {
        $normalizedText = strtolower($extractedText);
        $foundKeywords = [];
        $missingKeywords = [];

        foreach ($keywords as $keyword) {
            $normalizedKeyword = strtolower(trim($keyword));
            
            if (str_contains($normalizedText, $normalizedKeyword)) {
                $foundKeywords[] = $keyword;
            } else {
                $missingKeywords[] = $keyword;
            }
        }

        $totalKeywords = count($keywords);
        $foundCount = count($foundKeywords);
        $matchPercentage = $totalKeywords > 0 ? ($foundCount / $totalKeywords) * 100 : 0;
        return [
            'match_percentage' => round($matchPercentage, 2),
            'found_keywords' => $foundKeywords,
            'missing_keywords' => $missingKeywords,
            'total_keywords' => $totalKeywords,
            'found_count' => $foundCount
        ];
    }

    public function reprocessApplication(Application $application, KeywordSet $keywordSet)
    {
        if (empty($application->extracted_text)) {
            return $this->processApplication($application, $keywordSet);
        }

        // If text already extracted, just re-run keyword matching
        $matchResult = $this->matchKeywords($application->extracted_text, $keywordSet->keywords);
        
        $qualificationStatus = $this->determineQualificationStatus($matchResult['found_count'], $matchResult['total_keywords']);

        $application->update([
            'keyword_set_id' => $keywordSet->id,
            'qualification_status' => $qualificationStatus,
            'match_percentage' => $matchResult['match_percentage'],
            'found_keywords' => $matchResult['found_keywords'],
            'missing_keywords' => $matchResult['missing_keywords'],
            'processed_at' => now()
        ]);

        return [
            'success' => true,
            'match_result' => $matchResult
        ];
    }

    private function determineQualificationStatus($foundCount, $totalKeywords)
    {
        if ($totalKeywords > 0 && $foundCount === $totalKeywords) {
            return 'Qualified';
        }

        if ($foundCount >= 3) {
            return 'Fairly Qualified';
        }

        return 'Not Qualified';
    }
}
