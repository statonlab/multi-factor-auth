<?php

namespace Statonlab\MultiFactorAuth;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class MultiFactorAuthProvider extends LaravelServiceProvider
{
    /**
     * Boot.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/Views', 'mfa');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/multi_factor_auth.php' => config_path('multi_factor_auth.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/Http/Controllers/MultiFactorAuthController.php' => app_path('Http/Controllers/MultiFactorAuthController.php'),
            ], 'controllers');

            $this->publishes([
                __DIR__.'/Http/Middleware/VerifiedUser.php' => app_path('Http/Middleware/VerifiedUser.php'),
            ], 'middleware');

            $this->publishes([
                __DIR__.'/Views' => resource_path('views/vendor/mfa'),
            ], 'views');
        }
    }

    /**
     * Register.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/multi_factor_auth.php', 'multi_factor_auth');

        $this->app->bind('MultiFactorAuth', function () {
            return new Identity();
        });
    }
}