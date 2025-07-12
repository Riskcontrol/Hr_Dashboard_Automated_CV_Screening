<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\KeywordSet;
use App\Models\User;
use App\Services\CVProcessorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CVProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_processing_with_all_keywords_found()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $keywordSet = KeywordSet::create([
            'job_title' => 'Test Job',
            'keywords' => ['PHP', 'Laravel', 'MySQL'],
            'created_by' => $user->id
        ]);

        $application = Application::create([
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'cv_original_name' => 'test.pdf',
            'cv_stored_path' => 'test/path.pdf',
            'cv_file_size' => 1024,
            'extracted_text' => 'I have experience with PHP, Laravel, and MySQL databases'
        ]);

        $processor = new CVProcessorService();
        $result = $processor->reprocessApplication($application, $keywordSet);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['match_result']['qualified']);
        $this->assertEquals(100, $result['match_result']['match_percentage']);
    }

    public function test_cv_processing_with_missing_keywords()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $keywordSet = KeywordSet::create([
            'job_title' => 'Test Job',
            'keywords' => ['PHP', 'Laravel', 'React'],
            'created_by' => $user->id
        ]);

        $application = Application::create([
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'cv_original_name' => 'test.pdf',
            'cv_stored_path' => 'test/path.pdf',
            'cv_file_size' => 1024,
            'extracted_text' => 'I have experience with PHP and Laravel'
        ]);

        $processor = new CVProcessorService();
        $result = $processor->reprocessApplication($application, $keywordSet);

        $this->assertTrue($result['success']);
        $this->assertFalse($result['match_result']['qualified']);
        $this->assertContains('React', $result['match_result']['missing_keywords']);
    }
}