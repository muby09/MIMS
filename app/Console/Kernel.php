<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Daily report generation
        // $schedule->command('reports:generate')->daily()->at('02:00');

        // Weekly data cleanup
        // $schedule->command('data:cleanup')->weekly()->sundays()->at('03:00');

        // Queue monitoring
        // $schedule->command('queue:monitor')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
