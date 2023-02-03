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
            $pengawas = $user->role == 'pengawas';
            $admin = $user->role == 'administrator';
            if ($pengawas || $admin)
                return true;
        });

        Gate::define('isOwner', function (User $user, Laporan $laporan) {
            return $user->nik === $laporan->pelapor;
        });

        Gate::define('owner-and-petugas-can-open', function (User $user, Laporan $laporan) {
            if ($user->nik === $laporan->pelapor) {
                return true;
            } else if ($user->role === 'pengawas') {
                return true;
            }
        });
    }
}
