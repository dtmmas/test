<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * -d register_argc_argv=On C:\wamp64\www\DTM_sistema\laravel\artisan schedule:run
     * @var array
     */
    protected $commands = [
        Commands\EnviarRecordatorio::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        Log::error('schedule ', []);
        $schedule->command('EnviarRecordatorio')->monthlyOn(1, '10:00');
        //$schedule->command('EnviarRecordatorio')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
