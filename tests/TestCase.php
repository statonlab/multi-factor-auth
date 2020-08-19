<?php

namespace Statonlab\MultiFactorAuth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Statonlab\MultiFactorAuth\Facades\MultiFactorAuthFacade;
use Statonlab\MultiFactorAuth\MultiFactorAuthProvider;

class TestCase extends Orchestra
{
    /**
     * Add factories and other classes.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->loadMigrationsFrom(realpath(__DIR__.'/../src/Migrations'));
        $this->artisan('migrate');

        include_once __DIR__.'/TestUtilities/User.php';
        $this->withFactories(__DIR__.'/Factories');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup any env variables here
        // eg, $app['config']->set('app.name', 'MFA')
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            MultiFactorAuthProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array|string[]
     */
    protected function getApplicationAliases($app)
    {
        return [
            'MultiFactorAuth' => MultiFactorAuthFacade::class,
        ];
    }
}