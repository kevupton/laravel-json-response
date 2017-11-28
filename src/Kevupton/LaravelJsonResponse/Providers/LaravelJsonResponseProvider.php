<?php

namespace Kevupton\LaravelJsonResponse\Providers;

use Illuminate\Support\ServiceProvider;
use Kevupton\LaravelJsonResponse\JsonResponse;

class LaravelJsonResponseProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('eth.json', function () {
            return new JsonResponse();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}