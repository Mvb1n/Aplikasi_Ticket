<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
use App\Policies\SitePolicy;
use App\Policies\UserPolicy;
use App\Policies\AssetPolicy;
use App\Policies\IncidentPolicy;
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
        //
    }

    protected $policies = [
    Asset::class => AssetPolicy::class,
    User::class => UserPolicy::class,
    Incident::class => IncidentPolicy::class,
    Site::class => SitePolicy::class,
    ];
}
