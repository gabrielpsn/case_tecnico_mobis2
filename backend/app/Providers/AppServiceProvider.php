<?php

namespace App\Providers;

use App\Repositories\Contracts\MotoristaRepositoryInterface;
use App\Repositories\Eloquent\MotoristaRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            MotoristaRepositoryInterface::class,
            MotoristaRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
