<?php

declare(strict_types=1);

namespace Aainc\NewrelicLogger;

use Illuminate\Support\ServiceProvider;

class NewrelicLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/aa-newrelic-logger.php', 'aa-newrelic-logger');
        $this->publishes([
            __DIR__ . '/config/aa-newrelic-logger.php' => config_path('aa-newrelic-logger.php'),
        ]);
    }
}
