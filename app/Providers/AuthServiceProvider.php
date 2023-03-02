<?php

namespace App\Providers;

use App\Models\Laporan;
use App\Models\Masyarakat;
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
            return $user->role === 'pengawas';
        });

        Gate::define('only-admin', function (User $user) {
            return $user->role === 'administrator';
        });

        Gate::define('isOwner', function (User $user, Laporan $laporan) {
            $masy = Masyarakat::where('user_id', $user->id)->first();
            return $masy->nik === $laporan->nik;
        });

        Gate::define('owner-and-petugas-can-open', function (User $user, Laporan $laporan) {
            $masy = Masyarakat::where('user_id', $user->id)->first();

            if ($masy->nik === $laporan->nik) {
                return true;
            } else if ($user->role === 'pengawas') {
                return true;
            }
        });
    }
}
