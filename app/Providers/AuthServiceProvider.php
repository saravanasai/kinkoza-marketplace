<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Listing;
use App\Policies\CompanyPolicy;
use App\Policies\ListingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Listing::class, ListingPolicy::class);
    }
}
