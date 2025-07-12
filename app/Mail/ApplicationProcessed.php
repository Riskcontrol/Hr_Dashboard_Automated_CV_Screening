<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationProcessed extends Mailable
{
    use Queueable, SerializesModels;

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        $subject = $this->application->isQualified() 
            ? 'Congratulations! Your Application is Qualified'
            : 'Your Application Status Update';

        return $this->view('emails.application-processed')
                    ->with('application', $this->application)
                    ->subject($subject);
    }
}