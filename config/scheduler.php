<?php

use App\Jobs\CheckWantlistAvailability;

return [
    /*
    |--------------------------------------------------------------------------
    | Scheduled Tasks
    |--------------------------------------------------------------------------
    |
    | Here you may define all of your scheduled tasks for your application.
    | In Laravel 12, scheduled tasks are defined in this configuration file
    | instead of the Console\Kernel.php file used in previous versions.
    |
    */
    
    // Verifica a disponibilidade dos vinis da Wantlist todos os dias às 9:00
    [
        'job' => CheckWantlistAvailability::class,
        'schedule' => 'dailyAt',
        'params' => ['09:00'],
    ],
];
