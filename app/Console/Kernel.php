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
        //get rates
        $schedule->command('currency:update-currencies-rates')->everySixHours();
        //sync !c balance
        $schedule->command('sync:user-balance')->everyThreeHours();
        //sync new users
        $schedule->command('sync:caparts-users')->everySixHours();
        //relist trademe
        $schedule->command('trademe-listing:relist')->dailyAt('09:00');
        $schedule->command('trademe-listing:relist')->dailyAt('12:00');
        $schedule->command('trademe-listing:relist')->dailyAt('15:00');
        $schedule->command('trademe-listing:relist')->dailyAt('18:00');
        $schedule->command('trademe-listing:relist')->dailyAt('21:00');
        //live stock
        //$schedule->command('import:live-stock')->dailyAt('06:00');
        //$schedule->command('import:live-stock')->dailyAt('18:00');
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
