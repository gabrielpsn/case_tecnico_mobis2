<?php

namespace App\Providers;

use App\Repositories\Contracts\ManutencaoRepositoryInterface;
use App\Repositories\Contracts\MotoristaRepositoryInterface;
use App\Repositories\Contracts\VeiculoRepositoryInterface;
use App\Repositories\Eloquent\ManutencaoRepository;
use App\Repositories\Eloquent\MotoristaRepository;
use App\Repositories\Eloquent\VeiculoRepository;
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

        $this->app->bind(
            VeiculoRepositoryInterface::class,
            VeiculoRepository::class
        );

        $this->app->bind(
            ManutencaoRepositoryInterface::class,
            ManutencaoRepository::class
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
