<?php

namespace Ogrre\ApiAuth\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Laravel\Sanctum\SanctumServiceProvider;
use Ogrre\ApiAuth\Tests\TestModels\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Ogrre\ApiAuth\ApiAuthServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var string */
    protected $userClass;

    /** @var User */
    protected User $testUser;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userClass = config('auth.providers.users.model');

        $this->setUpDatabase($this->app);

        $this->testUser = $this->userClass::create([
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);
    }

    /**
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if (method_exists(AboutCommand::class, 'flushState')) {
            AboutCommand::flushState();
        }
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ApiAuthServiceProvider::class,
            SanctumServiceProvider::class
        ];
    }

    /**
     * Set up the environment.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('auth.guards.api', [
            'driver' => 'sanctum',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('sanctum.expiration', 60);
    }

    /**
     * Set up the database.
     *
     * @param Application $app
     */
    protected function setUpDatabase(Application $app): void
    {
        $schema = $app['db']->connection()->getSchemaBuilder();

        $this->artisan('migrate:fresh');

        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->artisan('migrate', ['--path' => 'vendor/laravel/sanctum/database/migrations']);
    }
}
