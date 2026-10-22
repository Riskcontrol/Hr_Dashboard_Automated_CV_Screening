<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run queue worker every minute (for shared hosting without long-running processes)
        $schedule->command('queue:work --stop-when-empty --timeout=50')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Clean up failed jobs older than 48 hours
        $schedule->command('queue:prune-failed --hours=48')
                 ->daily();

        // Email HR a countdown reminder (3, 2, then 1 day before) for
        // any staff member whose birthday is coming up.
        $schedule->command('birthdays:remind')
                 ->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}