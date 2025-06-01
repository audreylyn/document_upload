<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;
use App\Models\Applicant;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Make sure the Admin and Applicant models are registered in the container
        $this->app->bind('admin', function ($app) {
            return new Admin;
        });
        
        $this->app->bind('applicant', function ($app) {
            return new Applicant;
        });
    }
}
