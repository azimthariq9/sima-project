<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\BipaController;
use App\Http\Controllers\GunadarmaController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\API\KlnController;
use App\Http\Controllers\API\DokumenController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AnnouncementController;

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
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| GLOBAL PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES (🔥 FIX TOTAL)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:MAHASISWA'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        // ✅ COMPLETE PROFILE
        Route::get('complete-profile',  [MahasiswaController::class, 'completeProfile'])->name('complete-profile');
        Route::post('complete-profile', [MahasiswaController::class, 'storeCompleteProfile'])->name('complete-profile.store');

        // ✅ ROUTE SETELAH PROFILE COMPLETE
        Route::middleware(['profile.completed'])->group(function () {

            Route::get('dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');

            // 🔥 FIX: PROFIL → PROFILE
            Route::get('profile',   [MahasiswaController::class, 'getProfile'])->name('profile');
            Route::patch('profile', [MahasiswaController::class, 'updateProfile'])->name('profile.update');

            // REQUEST
            Route::get('request',         [MahasiswaRequestController::class, 'index'])->name('request.index');
            Route::post('request',        [MahasiswaRequestController::class, 'store'])->name('request.store');
            Route::get('request/create',  [MahasiswaController::class, 'createRequest'])->name('request.create');
            Route::post('request/quick',  [MahasiswaController::class, 'storeRequest'])->name('request.quick');

            // DOKUMEN
            Route::prefix('dokumen')->name('dokumen.')->group(function () {
                Route::get('/',             [DokumenController::class, 'index'])->name('index');
                Route::post('/',            [DokumenController::class, 'store'])->name('store');
                Route::get('{id}/download', [DokumenController::class, 'download'])->name('download');
            });

            Route::get('jadwal',       [MahasiswaController::class, 'jadwal'])->name('jadwal');
            Route::get('announcement', [MahasiswaController::class, 'announcement'])->name('announcement');
            Route::get('notifikasi',   [MahasiswaController::class, 'notifikasi'])->name('notifikasi');
            Route::get('analytics',    [MahasiswaController::class, 'analytics'])->name('analytics');
        });
    });

/*
|--------------------------------------------------------------------------
| DOSEN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'check.role:DOSEN'])->group(function () {
    Route::get('dashboard',   [DosenController::class, 'dashboard'])->name('dashboard');
    Route::get('profil',      [DosenController::class, 'profil'])->name('profil');
    Route::get('jadwal',      [DosenController::class, 'jadwal'])->name('jadwal');
    Route::get('announcement',[DosenController::class, 'announcement'])->name('announcement');
    Route::get('notifikasi',  [DosenController::class, 'notifikasi'])->name('notifikasi');
    Route::get('analytics',   [DosenController::class, 'analytics'])->name('analytics');

    Route::post('absensi/start', [DosenController::class, 'startAttendance'])->name('absensi.start');
    Route::post('absensi/close', [DosenController::class, 'closeAttendance'])->name('absensi.close');
});

/*
|--------------------------------------------------------------------------
| KLN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:KLN'])
    ->prefix('kln')->name('kln.')->group(function () {

        Route::get('dashboard', [KlnController::class, 'index'])->name('dashboard');
        Route::get('profil',    [KlnController::class, 'profil'])->name('profil');
        Route::get('analytics', [KlnController::class, 'analytics'])->name('analytics');

        Route::prefix('schedule')->name('schedule.')->group(function () {
            Route::get('/', [ScheduleController::class, 'index'])->name('index');
            Route::post('/', [ScheduleController::class, 'store'])->name('store');
        });
});

/*
|--------------------------------------------------------------------------
| JURUSAN, BIPA, GUNADARMA (TETAP)
|--------------------------------------------------------------------------
*/
Route::prefix('jurusan')->name('jurusan.')->middleware(['auth', 'check.role:JURUSAN'])->group(function () {
    Route::get('dashboard', [JurusanController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('bipa')->name('bipa.')->middleware(['auth', 'check.role:BIPA'])->group(function () {
    Route::get('dashboard', [BipaController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('gunadarma')->name('gunadarma.')->middleware(['auth', 'check.role:GUNADARMA'])->group(function () {
    Route::get('dashboard', [GunadarmaController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';