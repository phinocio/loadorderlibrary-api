<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('delete:temp')
            ->daily()
            ->onSuccess(function () {
                Log::channel('cleanup')->info('✅ Delete Temp Files');
            })
            ->onFailure(function () {
                Log::channel('cleanup')->error('❌ Delete Temp Files');
            })
            ->appendOutputTo(storage_path('logs/scheduled.log'));

        $schedule->command('delete:orphaned')
            ->daily()
            ->onSuccess(function () {
                Log::channel('cleanup')->info('✅ Delete Orphaned Files');
            })
            ->onFailure(function () {
                Log::channel('cleanup')->error('❌ Delete Orphaned Files');
            })
            ->appendOutputTo(storage_path('logs/scheduled.log'));

        $schedule->command('delete:expired')
            ->everyMinute()
            ->onSuccess(function () {
                Log::channel('cleanup')->info('✅ Delete Expired Lists');
            })
            ->onFailure(function () {
                Log::channel('cleanup')->error('❌ Delete Expired Lists');
            })
            ->appendOutputTo(storage_path('logs/scheduled.log'));
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
