<?php

namespace Tonysm\DbCreateCommand;

use Illuminate\Support\ServiceProvider;
use Tonysm\DbCreateCommand\Console\DbCreateCommand as DbCreateCLICommand;
use Tonysm\DbCreateCommand\Console\DbReCreateCommand as DbReCreateCLICommand;
use Tonysm\DbCreateCommand\Console\DbDropCommand as DbDropCLICommand;

class DbCreateCommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('dbcreatecommand.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                DbCreateCLICommand::class,
                DbReCreateCLICommand::class,
                DbDropCLICommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'dbcreatecommand');

        // Register the main class to use with the facade
        $this->app->singleton('dbcreatecommand', function () {
            return new DbCreateCommand;
        });
    }
}
