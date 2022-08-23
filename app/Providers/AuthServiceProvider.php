<?php

namespace App\Providers;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
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

        //
        // create gate only petugas
        Gate::define('only-petugas', function (User $user) {
            if ($user->role == 'petugas') {
                return true;
            }
        });

        Gate::define('only-owner-can-update-laporan', function (User $user, Laporan $laporan) {
            if ($user->id == $laporan->user_id) {
                return true;
            }
        });
    }
}
