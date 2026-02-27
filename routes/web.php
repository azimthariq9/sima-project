<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;
use App\Http\Controllers\API\KlnController;
use App\Http\Controllers\API\DokumenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('login1', [AuthenticatedSessionController::class, 'store'])->name('login1');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| GLOBAL DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| GLOBAL PROFILE (BREEZE)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| =========================
| KLN ROUTES (FINAL FIX)
| =========================
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:KLN'])
    ->prefix('kln')
    ->name('kln.')
    ->group(function () {

        

        Route::get('profile', [KlnController::class, 'profile'])
        ->name('profile');

        /* ---- MAIN ---- */
        Route::get('dashboard', [KlnController::class, 'index'])->name('dashboard');
        Route::view('monitoring', 'kln.monitoring')->name('monitoring');
        Route::view('validasi', 'kln.validasi')->name('validasi');
        Route::view('schedule', 'kln.schedule')->name('schedule');
        Route::view('analytics', 'kln.analytics')->name('analytics');
        Route::view('announcement', 'kln.announcement')->name('announcement');
        Route::view('notifikasi', 'kln.notifikasi')->name('notifikasi');
        Route::view('profil', 'kln.profil')->name('profil');

        /* ---- USERS ---- */
        Route::get('users', [KlnController::class, 'usersPage'])->name('users.page');
        Route::get('users-data', [KlnController::class, 'getUsers'])->name('users.data');
        Route::post('users', [KlnController::class, 'storeUser'])->name('users.store');
        Route::get('users/{id}', [KlnController::class, 'showUser'])->name('users.show');
        Route::put('users/{id}', [KlnController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{id}', [KlnController::class, 'destroyUser'])->name('users.destroy');
        Route::patch('users/{id}/status', [KlnController::class, 'updateStatusMahasiswa'])->name('users.status');

        /* ---- DOKUMEN (FINAL FIX) ---- */
        Route::get('dokumen', [KlnController::class, 'dokumen'])->name('dokumen');
        Route::get('dokumen/{id}', [KlnController::class, 'show'])->name('dokumen.show');
        Route::delete('dokumen/{id}', [KlnController::class, 'destroy'])->name('dokumen.destroy');
        Route::post('dokumen/{id}/upload', [KlnController::class, 'uploadFile'])->name('dokumen.upload');
        /* ---- REQUEST ---- */
        Route::prefix('requestDok')->name('request.')->group(function () {
            Route::get('/', [KlnController::class, 'indexReqDocument'])->name('index');
            Route::get('{id}', [KlnController::class, 'showReqDocument'])->name('show');
            Route::patch('{id}/status', [KlnController::class, 'updateReqDokumen'])->name('status');
            Route::post('{id}/upload', [KlnController::class, 'uploadReqDokumen'])->name('upload');
        });

        /* ---- ANNOUNCEMENT ---- */
        Route::post('announcement/store', [KlnController::class, 'storeAnnouncement'])->name('announcement.store');
    });
/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES (FINAL CLEAN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:MAHASISWA'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        // COMPLETE PROFILE
        Route::get('complete-profile', [MahasiswaController::class, 'completeProfile'])
            ->name('complete-profile');

        Route::post('complete-profile', [MahasiswaController::class, 'storeCompleteProfile'])
            ->name('complete-profile.store');
    });


Route::middleware(['auth', 'check.role:MAHASISWA', 'profile.completed'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('request/create', [MahasiswaController::class, 'createRequest'])
            ->name('request.create');

        Route::post('request/store', [MahasiswaController::class, 'storeRequest'])
            ->name('request.store');

        // DASHBOARD
        Route::get('dashboard', [MahasiswaController::class, 'dashboard'])
            ->name('dashboard');

        // PROFILE
        Route::get('profile', [MahasiswaController::class, 'getProfile'])
            ->name('profile');

        Route::patch('profile', [MahasiswaController::class, 'updateProfile'])
            ->name('profile.update');

        // REQUEST
        Route::get('request', [MahasiswaRequestController::class, 'index'])
            ->name('request.index');

        Route::post('request', [MahasiswaRequestController::class, 'store'])
            ->name('request.store');

        // DOKUMEN
        Route::prefix('dokumen')->name('dokumen.')->group(function () {

            Route::get('/', [DokumenController::class, 'index'])
                ->name('index');

            Route::post('/', [DokumenController::class, 'store'])
                ->name('store');

            Route::get('{id}/download', [DokumenController::class, 'download'])
                ->name('download');
        });

        Route::view('jadwal', 'mahasiswa.jadwal')->name('jadwal');

        Route::view('announcement', 'mahasiswa.announcement')->name('announcement');

        Route::view('notifikasi', 'mahasiswa.notifikasi')->name('notifikasi');

        Route::get('analytics', [MahasiswaController::class, 'analytics'])
        ->name('analytics');

    });


     Route::patch('users/status/{id}', [KlnController::class, 'updateStatusMahasiswa'])->name('users.status.update')->middleware(['auth','check.role:KLN']);


// routes/web.php - TEMPORARY
Route::get('debug-middleware', function() {
    $routes = collect(Route::getRoutes())->map(function($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'middleware' => $route->gatherMiddleware(),
            'action' => $route->getActionName()
        ];
    })->filter(function($route) {
        return str_contains($route['uri'], 'announcement') || 
               str_contains($route['uri'], 'status');
    })->values();
    
    return response()->json($routes);
})->middleware('auth');

Route::get('test-role/{role}', function($role) {
    return response()->json([
        'user_role' => auth()->user()->role,
        'required_role' => $role,
        'can_access' => true
    ]);
})->middleware(['auth', 'check.role:adminJurusan']);



Route::get('test-update', function() {
    $user = Auth::user();
    $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
    
    return response()->json([
        'before' => $mahasiswa,
        'try_update' => $mahasiswa->update(['nama' => 'marcello']),
        'after' => $mahasiswa->fresh(),
    ]);
})->middleware('auth');



Route::prefix('dosen')
    ->name('dosen.')
    ->middleware(['auth','check.role:DOSEN'])
    ->group(function () {

        Route::get('/dashboard', [DosenController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/profil', [DosenController::class, 'profil'])
            ->name('profil');

        Route::get('/jadwal', [DosenController::class, 'jadwal'])
            ->name('jadwal');

        Route::get('/announcement', [DosenController::class, 'announcement'])
            ->name('announcement');

        Route::get('/notifikasi', [DosenController::class, 'notifikasi'])
            ->name('notifikasi');

        Route::get('/analytics', [DosenController::class, 'analytics'])
            ->name('analytics');

});
require __DIR__.'/auth.php';