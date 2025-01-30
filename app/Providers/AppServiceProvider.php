<?php

namespace App\Providers;

use App\Repository\Contracts\TravelOrderRepository;
use App\Repository\TravelOrderRepositoryEloquent;
use Illuminate\Support\ServiceProvider;
use App\Repository\Contracts\UserRepository;
use App\Repository\UserRepositoryEloquent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(TravelOrderRepository::class, TravelOrderRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
