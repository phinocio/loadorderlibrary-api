<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\LoadOrder;
use App\Observers\GameObserver;
use App\Observers\LoadOrderObserver;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\IpUtils;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        // Register observers
        Game::observe(GameObserver::class);
        LoadOrder::observe(LoadOrderObserver::class);

        RateLimiter::for('api', function (Request $request) {
            // Hopefully means no limit for requests from frontend server itself.
            $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? ''; //If key isn't set, for example in tests, just use empty string.
            if (IpUtils::checkIp($remoteAddr, '178.128.235.79')) {
                return Limit::none();
            }

            return Limit::perMinute(200)->by($request->user()?->id ?: $request->ip());
        });
    }
}
