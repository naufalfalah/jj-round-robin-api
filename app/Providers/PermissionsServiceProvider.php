<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Gate::before(function ($user, $ability) {
                if ($user->is_admin) {
                    return true;
                }
            });
            Permission::get(['slug'])->map(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission->slug);
                });
            });
        } catch (\Exception $e) {
            report($e);
            return false;
        }

        Blade::if('roles', function ($value) {
            return auth()->check() && auth()->user()->hasRole($value);
        });
    }
}
