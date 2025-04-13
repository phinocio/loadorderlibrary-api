<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            // @codeCoverageIgnoreStart
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            // @codeCoverageIgnoreEnd
        }
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureDates();
        $this->configureUrls();
    }

    /** Configure the application's commands. */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            (bool) $this->app->environment('production')
        );
    }

    /** Configure the application's dates. */
    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /** Configure the application's models. */
    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }

    /** Configure the application's URLs. */
    private function configureUrls(): void
    {
        URL::forceScheme('https');
    }
}
