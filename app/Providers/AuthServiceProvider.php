<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Import model dan policy yang kamu pakai
use App\Models\Activity;
use App\Policies\ActivityPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        // Tambahkan model lainnya di sini kalau perlu
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('viewAny', [ActivityPolicy::class, 'viewAny']);
        Gate::define('create', [ActivityPolicy::class, 'create']);
        Gate::define('update', [ActivityPolicy::class, 'update']);
        Gate::define('delete', [ActivityPolicy::class, 'delete']);
    }
}
