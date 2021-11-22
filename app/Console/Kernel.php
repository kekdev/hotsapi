<?php

namespace App\Console;

use App\Console\Commands\FetchTalents;
use App\Console\Commands\FetchTranslations;
use App\Console\Commands\ReparseReplays;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(FetchTranslations::class)->daily();
        $schedule->command(FetchTalents::class)->daily();
    }

    /**
     * Register the Closure based commands for the application.
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
