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
        // Envía los datos a la API PORTICO en horarios fijos: 00:00, 06:00,
        // 12:00 y 18:00. La app de escritorio los descarga 5 min después
        // (00:05, 06:05, ...), cuando la API ya tiene los datos frescos.
        $schedule->command('sync:portico')
            ->cron('0 0,6,12,18 * * *')
            ->withoutOverlapping()
            ->runInBackground();
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
