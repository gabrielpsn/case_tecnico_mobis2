<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\Connection as MongoDBConnection;

class MongoDBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register MongoDB connection
        $this->app->singleton('mongodb', function ($app) {
            $config = $app->make('config')->get('database.mongodb', []);

            return new MongoDBConnection($config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add MongoDB connection to DB facade
        DB::extend('mongodb', function ($config, $name) {
            $config['name'] = $name;

            return new MongoDBConnection($config);
        });
    }
}
