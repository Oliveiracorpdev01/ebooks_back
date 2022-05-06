<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
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
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-post', function (User $user, $route_name) {
            $permissions = Permission::with('roles')->get();
            foreach ($permissions as $permission) {
                foreach ($user->roles as $role) {
                    if ($role->permissions->contains('name', $route_name)) {
                        return true;
                    }                    
                }
                return false;
            }
        });

    }
}
