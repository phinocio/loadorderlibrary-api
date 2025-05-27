<?php

declare(strict_types=1);

use App\Http\Middleware\DenyAuthenticated;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        apiPrefix: '',
        api: __DIR__.'/../routes/api/api.php',
        commands: __DIR__.'/../routes/console.php',
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
        $middleware->statefulApi();

        $middleware->alias([
            'deny.authenticated' => DenyAuthenticated::class,
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => str($previous->getModel())->afterLast('\\').' not found',
                    'status' => Response::HTTP_NOT_FOUND,
                ], Response::HTTP_NOT_FOUND);
            }
        });
    })->withSchedule(function (Schedule $schedule) {
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
        $schedule->command('lists:delete-expired')->everyMinute();
        $schedule->command('lists:delete-orphaned')->daily();
    })->create();
