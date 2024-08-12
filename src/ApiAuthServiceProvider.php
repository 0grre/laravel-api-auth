<?php

namespace Ogrre\ApiAuth;

use Illuminate\Support\ServiceProvider;

class ApiAuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-api-auth');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-api-auth'),
            __DIR__.'/../config/api-auth.php' => config_path('api-auth.php'),
        ], 'laravel-api-auth');

        $this->publishes([
            __DIR__.'/Notifications/ApiResetPasswordNotification.php' => app_path('Notifications/ApiResetPasswordNotification.php'),
        ], 'laravel-api-auth-custom-reset');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/api-auth.php', 'api-auth'
        );
    }
}
