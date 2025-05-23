<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AppointmentServiceInterface;
use App\Services\AppointmentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(
            AppointmentServiceInterface::class,
            AppointmentService::class
        );
        $this->app->bind(
            \App\Services\AuthServiceInterface::class,
            \App\Services\AuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        //
    }
}
