<?php

namespace Keenops\Mpesa;

use Illuminate\Support\ServiceProvider;

class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        if ($this->app->runningInConsole() && function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../config/laravel-mpesa.php' => config_path('laravel-mpesa.php'),
            ], 'laravel-mpesa');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-mpesa.php', 'laravel-mpesa');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-mpesa', function () {
            return new Mpesa;
        });
    }
}
