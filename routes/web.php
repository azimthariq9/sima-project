<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;
use App\Http\Controllers\API\KlnController;

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
| Dashboard (Redirect Logic)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

        Route::get('/dashboard', fn() => view('mahasiswa.dashboard'))->name('dashboard');
        Route::get('/profil', fn() => view('mahasiswa.profil'))->name('profil');
        Route::get('/profil/dokumen', fn() => view('mahasiswa.profil'))->name('profil.dokumen');
        Route::get('/jadwal', fn() => view('mahasiswa.jadwal'))->name('jadwal');
        Route::get('/notifikasi', fn() => view('mahasiswa.notifikasi'))->name('notifikasi');
        Route::get('/announcement', fn() => view('mahasiswa.announcement'))->name('announcement');
        Route::get('/analytics', fn() => view('mahasiswa.analytics'))->name('analytics');

        Route::get('/request', [MahasiswaRequestController::class, 'index'])->name('request.index');
        Route::get('/request/create', [MahasiswaRequestController::class, 'create'])->name('request.create');
        Route::post('/request', [MahasiswaRequestController::class, 'store'])->name('request.store');
});
/*
|--------------------------------------------------------------------------
| KLN ROUTES (FULL FIX - NO ERROR)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:KLN'])
    ->prefix('kln')
    ->name('kln.')
    ->group(function () {

        // ===== DASHBOARD =====
        Route::get('/dashboard', [KlnController::class, 'index'])->name('dashboard');

        // ===== HALAMAN VIEW (SEMUA YANG DIPAKAI DI DASHBOARD) =====
        Route::get('/monitoring', fn() => view('kln.monitoring'))->name('monitoring');
        Route::get('/validasi', fn() => view('kln.validasi'))->name('validasi');
        Route::get('/analytics', fn() => view('kln.analytics'))->name('analytics');
        Route::get('/schedule', fn() => view('kln.schedule'))->name('schedule');
        Route::get('/announcement', fn() => view('kln.announcement'))->name('announcement');

        Route::post('/announcement/store', function () {
            return back()->with('success', 'Pengumuman berhasil dikirim');
        })->name('announcement.store');

        Route::get('/users', [KlnController::class, 'usersPage'])->name('users.page');
        Route::get('/dokumen', [KlnController::class, 'dokumenPage'])->name('dokumen.page');

        // ===== API INTERNAL =====
        Route::prefix('api')->name('api.')->group(function () {

            Route::get('dashboard/stats', [KlnController::class, 'getDashboardStats'])->name('dashboard.stats');

            Route::get('users', [KlnController::class, 'getUsers'])->name('users.index');
            Route::post('users', [KlnController::class, 'storeUser'])->name('users.store');
            Route::get('users/{id}', [KlnController::class, 'showUser'])->name('users.show');
            Route::put('users/{id}', [KlnController::class, 'updateUser'])->name('users.update');
            Route::delete('users/{id}', [KlnController::class, 'destroyUser'])->name('users.destroy');
            Route::put('users/{id}/status', [KlnController::class, 'updateStatusMahasiswa'])->name('users.status');

            Route::get('dokumen', [KlnController::class, 'getDokumen'])->name('dokumen.index');
            Route::put('dokumen/{id}/status', [KlnController::class, 'updateDokumenStatus'])->name('dokumen.status');
        });
});

/*
|--------------------------------------------------------------------------
| Breeze Auth
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';