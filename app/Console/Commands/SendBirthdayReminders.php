<?php

namespace App\Console\Commands;

use App\Mail\BirthdayReminder;
use App\Models\StaffProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

/**
 * SendBirthdayReminders
 *
 * Run once a day. For every active staff member whose birthday falls
 * exactly 3, 2, or 1 days from today, sends HR a reminder email. Because
 * this runs daily, the same staff member naturally triggers 3 separate
 * emails on 3 consecutive days as their birthday approaches.
 *
 * Schedule: registered in app/Console/Kernel.php to run daily.
 */
class SendBirthdayReminders extends Command
{
    protected $signature = 'birthdays:remind';

    protected $description = 'Email HR a reminder for staff birthdays happening in 3, 2, or 1 day(s)';

    public function handle(): int
    {
        $today = Carbon::today();
        $remindersSent = 0;

        $staff = StaffProfile::where('status', 'active')
            ->whereNotNull('date_of_birth')
            ->get();

        foreach ($staff as $staffProfile) {
            $daysUntil = $this->daysUntilNextBirthday($staffProfile->date_of_birth, $today);

            if (in_array($daysUntil, [1, 2, 3], true)) {
                Mail::to(config('hr.notification_email'))
                    ->send(new BirthdayReminder($staffProfile, $daysUntil));

                $remindersSent++;

                $this->info("Reminder sent for {$staffProfile->full_name} ({$daysUntil} day(s) to go).");
            }
        }

        $this->info("Done. {$remindersSent} birthday reminder(s) sent.");

        return self::SUCCESS;
    }

    /**
     * Work out how many days from $today until this person's NEXT birthday,
     * handling the year-end wraparound (e.g. today is Dec 30, birthday is Jan 1).
     */
    private function daysUntilNextBirthday(Carbon $dateOfBirth, Carbon $today): int
    {
        $nextBirthday = Carbon::create($today->year, $dateOfBirth->month, $dateOfBirth->day);

        if ($nextBirthday->lt($today)) {
            $nextBirthday = Carbon::create($today->year + 1, $dateOfBirth->month, $dateOfBirth->day);
        }

        return $today->diffInDays($nextBirthday);
    }
}