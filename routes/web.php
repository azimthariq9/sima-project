<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Custom
|--------------------------------------------------------------------------
*/

Route::post('login1', [AuthenticatedSessionController::class, 'store'])->name('login1');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard (Semua Role - Redirect Logic di Controller)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Profile (Semua User Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| KLN / ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:admin'])->group(function () {

    Route::apiResource('users', UserController::class);

});

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

    Route::get('/dashboard', function () {
        return view('mahasiswa.dashboard');
    })->name('dashboard');

    Route::get('/request', [MahasiswaRequestController::class, 'index'])
        ->name('request.index');

    Route::get('/request/create', [MahasiswaRequestController::class, 'create'])
        ->name('request.create');

    Route::post('/request', [MahasiswaRequestController::class, 'store'])
        ->name('request.store');

    Route::get('/profil', function () {
        return view('mahasiswa.profil');
    })->name('profil');

    Route::get('/profil/dokumen', function () {
        return view('mahasiswa.profil');
    })->name('profil.dokumen');

    Route::get('/jadwal', function () {
        return view('mahasiswa.jadwal');
    })->name('jadwal');

    Route::get('/notifikasi', function () {
        return view('mahasiswa.notifikasi');
    })->name('notifikasi');

    Route::get('/announcement', function () {
        return view('mahasiswa.announcement');
    })->name('announcement');

    Route::get('/analytics', function () {
        return view('mahasiswa.analytics');
    })->name('analytics');

});
/*
|--------------------------------------------------------------------------
| Include Breeze Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';