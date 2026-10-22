<?php

namespace App\Mail;

use App\Models\StaffProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * BirthdayReminder
 *
 * Sent to HR ahead of a staff member's birthday. The same staff member
 * triggers this 3 separate times in the 3 days leading up to their
 * birthday (3 days before, 2 days before, 1 day before), so HR gets a
 * countdown of reminders rather than a single one-off email.
 */
class BirthdayReminder extends Mailable
{
    use Queueable, SerializesModels;

    public StaffProfile $staffProfile;
    public int $daysUntil;

    public function __construct(StaffProfile $staffProfile, int $daysUntil)
    {
        $this->staffProfile = $staffProfile;
        $this->daysUntil    = $daysUntil;
    }

    public function build()
    {
        $dayWord = $this->daysUntil === 1 ? 'day' : 'days';

        return $this->view('emails.birthday-reminder')
            ->with([
                'staffProfile' => $this->staffProfile,
                'daysUntil'    => $this->daysUntil,
            ])
            ->subject("Upcoming Birthday: {$this->staffProfile->full_name} in {$this->daysUntil} {$dayWord}");
    }
}