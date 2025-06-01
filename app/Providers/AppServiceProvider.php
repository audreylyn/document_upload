<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Admin;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Admin model with the container
        $this->app->singleton(Admin::class, function ($app) {
            return new Admin;
        });
        
        // Register the Applicant model with the container
        $this->app->singleton(Applicant::class, function ($app) {
            return new Applicant;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
