<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\KeywordSet;
use App\Services\CVProcessorService;
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
    protected $keywordSetId;

    public function __construct(Application $application, ?int $keywordSetId = null)
    {
        $this->application = $application;
        $this->keywordSetId = $keywordSetId;
    }

    public function handle(CVProcessorService $processor)
    {
        Log::info('Processing CV job started', [
            'application_id' => $this->application->id,
            'keyword_set_id' => $this->keywordSetId
        ]);

        $keywordSet = null;
        if ($this->keywordSetId) {
            $keywordSet = KeywordSet::find($this->keywordSetId);
        }

        $result = $processor->processApplication($this->application, $keywordSet);

        if ($result['success']) {
            Log::info('CV processing completed successfully', [
                'application_id' => $this->application->id,
                'qualified' => $this->application->fresh()->isQualified()
            ]);
        } else {
            Log::error('CV processing failed', [
                'application_id' => $this->application->id,
                'error' => $result['error']
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CV processing job failed completely', [
            'application_id' => $this->application->id,
            'error' => $exception->getMessage()
        ]);

        $this->application->update(['qualification_status' => 'failed']);
    }
}