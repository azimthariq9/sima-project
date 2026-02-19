<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('mahasiswa', fn($user) => $user->role === 'mahasiswa');
        Gate::define('kln', fn($user) => $user->role === 'kln');
        Gate::define('jurusan', fn($user) => $user->role === 'jurusan');
        Gate::define('bipa', fn($user) => $user->role === 'bipa');
        Gate::define('admin', fn($user) => $user->role === 'admin');
    }
}
