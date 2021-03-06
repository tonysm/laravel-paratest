<?php

namespace Tonysm\LaravelParatest\Testing;

use Illuminate\Contracts\Console\Kernel;

/**
 * Trait RefreshDatabase
 *
 * Most of the code here was copied from Laravel's RefreshDatabase trait.
 *
 * @package Tonysm\DbCreateCommand\Testing
 *
 * @see https://github.com/laravel/framework/blob/5.8/src/Illuminate/Foundation/Testing/RefreshDatabase.php
 */
trait RefreshDatabase
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase()
    {
        $this->usingInMemoryDatabase()
            ? $this->refreshInMemoryDatabase()
            : $this->refreshTestDatabase();
    }

    /**
     * Determine if an in-memory database is being used.
     *
     * @return bool
     */
    protected function usingInMemoryDatabase()
    {
        $default = config('database.default');

        return config("database.connections.$default.database") === ':memory:';
    }

    /**
     * Refresh the in-memory database.
     *
     * @return void
     */
    protected function refreshInMemoryDatabase()
    {
        $this->artisan('migrate');

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        $this->swapTestingDatabase();

        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('db:create');
            $this->artisan('migrate:fresh', $this->shouldDropViews() ? [
                '--drop-views' => true,
            ] : []);

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    /**
     * Begin a database transaction on the testing database.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            $connection = $database->connection($name);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->beginTransaction();
            $connection->setEventDispatcher($dispatcher);
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = $database->connection($name);
                $dispatcher = $connection->getEventDispatcher();

                $connection->unsetEventDispatcher();
                $connection->rollback();
                $connection->setEventDispatcher($dispatcher);
                $connection->disconnect();
            }
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return array
     */
    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }

    /**
     * Determine if views should be dropped when refreshing the database.
     *
     * @return bool
     */
    protected function shouldDropViews()
    {
        return property_exists($this, 'dropViews')
            ? $this->dropViews : false;
    }

    protected function swapTestingDatabase(): void
    {
        $driver = config('database.default');
        $dbName = config("database.connections.{$driver}.database");

        // Paratest gives each process a unique TEST_TOKEN env variable.
        // When that's not set, we can default to 1 because it's
        // probably running on PHPUnit instead.
        config([
            "database.connections.{$driver}.database" => sprintf(
                '%s_test_%s',
                $dbName,
                env('TEST_TOKEN', 1)
            ),
        ]);
    }
}
