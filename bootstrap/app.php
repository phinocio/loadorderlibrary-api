<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: ['172.20.0.0/24'],
            headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
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
                // Log::channel('cleanup')->info('✅ Delete Expired Lists');
            })
            ->onFailure(function () {
                Log::channel('cleanup')->error('❌ Delete Expired Lists');
            })
            ->appendOutputTo(storage_path('logs/scheduled.log'));

        $schedule->command('backup:run --only-files')
            ->daily()->at('01:00')
            ->environments(['production', 'testing'])
            ->onSuccess(function () {
                Log::channel('backups')->info('✅ Clean Backups');
            })
            ->onFailure(function () {
                Log::channel('backups')->error('❌ Clean Backups');
            })
            ->appendOutputTo(storage_path('logs/backups.log'));

        $schedule->command('backup:clean')
            ->daily()->at('01:30')
            ->environments(['production', 'testing'])
            ->onSuccess(function () {
                Log::channel('backups')->info('✅ Clean Backups');
            })
            ->onFailure(function () {
                Log::channel('backups')->error('❌ Clean Backups');
            })
            ->appendOutputTo(storage_path('logs/backups.log'));
    })->create();
