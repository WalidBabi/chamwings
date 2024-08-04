<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     * @var array
     */

    protected $commands = [
        \App\Console\Commands\RunSegmentation::class,
    ];
    
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('delete:code')->everyFifteenMinutes();
        $schedule->command('segmentation:run')
        ->daily()
        ->at('02:00')  // Run at 2 AM
        ->appendOutputTo(storage_path('logs/segmentation.log'));
        //php artisan segmentation:run
        //php artisan schedule:list
        //php artisan schedule:work
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}