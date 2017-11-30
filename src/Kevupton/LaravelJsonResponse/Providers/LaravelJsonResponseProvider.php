<?php

namespace Kevupton\LaravelJsonResponse\Providers;

use Illuminate\Support\ServiceProvider;
use Kevupton\LaravelJsonResponse\JsonResponse;

class LaravelJsonResponseProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot ()
    {
        $this->publishes([__DIR__ . '/../../../config/config.php' => config_path(LARAVEL_JSON_RESPONSE_CONFIG . '.php')]);

        $this->app->singleton(LARAVEL_JSON_RESPONSE_KEY, function () {
            return new JsonResponse();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register ()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/config.php', LARAVEL_JSON_RESPONSE_CONFIG
        );
    }
}