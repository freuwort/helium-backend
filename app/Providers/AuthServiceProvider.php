<?php

namespace App\Providers;

use App\Classes\Permissions\Permissions;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all abilities to users with the super admin permission
        Gate::after(function ($user, $ability) {
            return $user->hasAnyPermission([Permissions::SYSTEM_SUPER_ADMIN, Permissions::SYSTEM_ADMIN]);
        });
    }
}
