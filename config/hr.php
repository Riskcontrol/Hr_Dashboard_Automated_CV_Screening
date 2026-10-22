<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HR Notification Email
    |--------------------------------------------------------------------------
    |
    | Used for automated HR notifications, such as upcoming staff birthday
    | reminders. Falls back to the app's default "from" address if not set.
    | Set HR_NOTIFICATION_EMAIL in your .env file to the real HR inbox.
    |
    */
    'notification_email' => env('HR_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS', 'hr@example.com')),

];