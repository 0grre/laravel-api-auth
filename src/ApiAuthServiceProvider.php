<?php

namespace Ogrre\ApiAuth;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Ogrre\ApiAuth\Exceptions\ApiAuthExceptionHandler;

class ApiAuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'laravel-api-auth');

        $this->publishes([
            __DIR__.'/config/api-auth.php' => config_path('api-auth.php'),
        ], 'laravel-api-auth');

        $this->app->singleton(ExceptionHandler::class, ApiAuthExceptionHandler::class);

        // Optional: Publish the custom reset password notification
        $this->publishes([
            __DIR__.'/Notifications/ApiResetPasswordNotification.php' => app_path('Notifications/ApiResetPasswordNotification.php'),
        ], 'laravel-api-auth-custom-reset');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/api-auth.php', 'api-auth'
        );

        $this->app->bind('ApiAuthUser', function ($app) {
            return new (config('auth.providers.users.model'));
        });
    }
}
