<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\KeywordSet;
use App\Services\GitHubActionsProcessorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCVJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $application;
    protected $keywordSet;

    public function __construct(Application $application, KeywordSet $keywordSet)
    {
        $this->application = $application;
        $this->keywordSet = $keywordSet;
    }

    public function handle(GitHubActionsProcessorService $processor)
    {
        if (!$this->keywordSet) {
            $this->application->update(['processing_status' => 'failed']);
            Log::error('CV processing job failed: KeywordSet is null.', [
                'application_id' => $this->application->id,
            ]);
            $this->fail(new \Exception('KeywordSet is null.'));
            return;
        }

        $this->application->update([
            'processing_status' => 'processing',
            'processing_started_at' => now(),
        ]);

        Log::info('Processing CV job started', [
            'application_id' => $this->application->id,
            'keyword_set_id' => $this->keywordSet->id
        ]);

        try {
            $processor->processApplication($this->application, $this->keywordSet);
            
            // The GitHub action will notify the application of completion via a callback.
            // We don't mark it as 'completed' here.

        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->application->update(['processing_status' => 'failed']);

        Log::error('CV processing job failed completely', [
            'application_id' => $this->application->id,
            'error' => $exception->getMessage()
        ]);
    }
}
