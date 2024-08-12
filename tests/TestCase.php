<?php

namespace Ogrre\ApiAuth\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Ogrre\ApiAuth\ApiAuthServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param $app
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ApiAuthServiceProvider::class,
        ];
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Use in-memory SQLite for testing
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up mailer to log emails instead of sending them
        $app['config']->set('mail.default', 'log');

        // Any other configuration specific to your package
        $app['config']->set('auth.providers.users.model', \App\Models\User::class);

        // You can also dynamically adjust the config for your package
        $app['config']->set('laravelapiauth.route_prefix', 'api/auth');
    }
}
