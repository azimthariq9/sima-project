<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\API\KlnController;
use App\Http\Controllers\API\DokumenController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DosenController as ApiDosenController;
use App\Http\Controllers\API\JurusanController as ApiJurusanController;
use App\Http\Controllers\API\MatakuliahController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\MahasiswaKelasController;
use App\Http\Controllers\API\JadwalController;
use App\Http\Controllers\API\KehadiranController;

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
| MAHASISWA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:MAHASISWA'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('complete-profile',  [MahasiswaController::class, 'completeProfile'])->name('complete-profile');
        Route::post('complete-profile', [MahasiswaController::class, 'storeCompleteProfile'])->name('complete-profile.store');

        Route::middleware(['profile.completed'])->group(function () {

            Route::get('dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
            Route::get('profile',   [MahasiswaController::class, 'getProfile'])->name('profile');
            Route::patch('profile', [MahasiswaController::class, 'updateProfile'])->name('profile.update');

            Route::get('request',        [MahasiswaRequestController::class, 'index'])->name('request.index');
            Route::post('request',       [MahasiswaRequestController::class, 'store'])->name('request.store');
            Route::get('request/create', [MahasiswaController::class, 'createRequest'])->name('request.create');
            Route::post('request/quick', [MahasiswaController::class, 'storeRequest'])->name('request.quick');

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
| JURUSAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:JURUSAN'])
    ->prefix('jurusan')
    ->name('jurusan.')
    ->group(function () {

        // Halaman konten (via page controller)
        Route::get('dashboard',    [JurusanController::class, 'dashboard'])->name('dashboard');
        Route::get('notifikasi',   [JurusanController::class, 'notifikasi'])->name('notifikasi');
        Route::get('profil',       [JurusanController::class, 'profil'])->name('profil');
        Route::get('announcement', [JurusanController::class, 'announcement'])->name('announcement');

        // Dokumen
        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/',              [JurusanController::class, 'dokumen'])->name('index');
            Route::get('{id}',           [JurusanController::class, 'showDokumen'])->name('show');
            Route::post('{id}/upload',   [JurusanController::class, 'uploadDokumen'])->name('upload');
        });

        // Request dokumen
        Route::prefix('requestDok')->name('request.')->group(function () {
            Route::get('/',              [JurusanController::class, 'indexReqDocument'])->name('index');
            Route::get('{id}',           [JurusanController::class, 'showReqDocument'])->name('show');
            Route::patch('{id}/status',  [JurusanController::class, 'updateReqDokumen'])->name('status');
            Route::post('{id}/upload',   [JurusanController::class, 'uploadReqDokumen'])->name('upload');
        });

        // Halaman manajemen (via ApiJurusanController — punya page methods)
        Route::get('dosen',      [ApiJurusanController::class, 'dosenPage'])->name('dosen.page');
        Route::get('matakuliah', [ApiJurusanController::class, 'matakuliahPage'])->name('matakuliah.page');
        Route::get('kelas',      [ApiJurusanController::class, 'kelasPage'])->name('kelas.page');
        Route::get('jadwal',     [ApiJurusanController::class, 'jadwalPage'])->name('jadwal.page');
        Route::get('mahasiswa',  [ApiJurusanController::class, 'mahasiswaPage'])->name('mahasiswa.page');

        // AJAX — Dosen
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('data',    [ApiDosenController::class, 'getData'])->name('data');
            Route::get('preview', [ApiJurusanController::class, 'previewDosen'])->name('preview');
            Route::get('{id}',    [ApiDosenController::class, 'show'])->name('show');
            Route::post('/',      [ApiDosenController::class, 'store'])->name('store');
            Route::patch('{id}',  [ApiDosenController::class, 'update'])->name('update');
            Route::delete('{id}', [ApiDosenController::class, 'destroy'])->name('destroy');
        });

        // AJAX — User (akun dosen)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('data',     [UserController::class, 'getUsers'])->name('data');
            Route::get('{id}',     [UserController::class, 'showUser'])->name('show');
            Route::post('/',       [UserController::class, 'store'])->name('store');
            Route::patch('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{id}',  [UserController::class, 'destroy'])->name('destroy');
        });

        // AJAX — Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('data', [ApiJurusanController::class, 'getMahasiswaData'])->name('data');
            Route::get('{id}', [ApiJurusanController::class, 'showMahasiswa'])->name('show');
        });

        // AJAX — Matakuliah
        Route::prefix('matakuliah')->name('matakuliah.')->group(function () {
            Route::get('data',    [MatakuliahController::class, 'getData'])->name('data');
            Route::get('preview', [ApiJurusanController::class, 'previewMatakuliah'])->name('preview');
            Route::get('{id}',    [MatakuliahController::class, 'show'])->name('show');
            Route::post('/',      [MatakuliahController::class, 'store'])->name('store');
            Route::patch('{id}',  [MatakuliahController::class, 'update'])->name('update');
            Route::delete('{id}', [MatakuliahController::class, 'destroy'])->name('destroy');
        });

        // AJAX — Kelas
        Route::prefix('kelas')->name('kelas.')->group(function () {
            Route::get('data',    [KelasController::class, 'getData'])->name('data');
            Route::get('preview', [ApiJurusanController::class, 'previewKelas'])->name('preview');
            Route::get('{id}',    [KelasController::class, 'show'])->name('show');
            Route::post('/',      [KelasController::class, 'store'])->name('store');
            Route::patch('{id}',  [KelasController::class, 'update'])->name('update');
            Route::delete('{id}', [KelasController::class, 'destroy'])->name('destroy');

            Route::prefix('{kelasId}/mahasiswa')->name('mahasiswa.')->group(function () {
                Route::get('/',                [MahasiswaKelasController::class, 'getMahasiswa'])->name('get');
                Route::post('/',               [MahasiswaKelasController::class, 'addMahasiswa'])->name('add');
                Route::delete('{mahasiswaId}', [MahasiswaKelasController::class, 'removeMahasiswa'])->name('remove');
            });
        });

        // AJAX — Jadwal
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('data',    [JadwalController::class, 'getData'])->name('data');
            Route::get('{id}',    [JadwalController::class, 'show'])->name('show');
            Route::post('/',      [JadwalController::class, 'store'])->name('store');
            Route::patch('{id}',  [JadwalController::class, 'update'])->name('update');
            Route::delete('{id}', [JadwalController::class, 'destroy'])->name('destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| DOSEN ROUTES (satu group, tidak duplikat)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:DOSEN'])
    ->prefix('dosen')
    ->name('dosen.')
    ->group(function () {

        Route::get('dashboard',    [DosenController::class, 'dashboard'])->name('dashboard');
        Route::get('profil',       [DosenController::class, 'profil'])->name('profil');
        Route::get('announcement', [DosenController::class, 'announcement'])->name('announcement');
        Route::get('notifikasi',   [DosenController::class, 'notifikasi'])->name('notifikasi');
        Route::get('analytics',    [DosenController::class, 'analytics'])->name('analytics');

        Route::post('absensi/start', [DosenController::class, 'startAttendance'])->name('absensi.start');
        Route::post('absensi/close', [DosenController::class, 'closeAttendance'])->name('absensi.close');

        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('/',                     [DosenController::class, 'jadwal'])->name('index');
            Route::get('{jadwalId}',            [DosenController::class, 'jadwalDetail'])->name('detail');
            Route::patch('{jadwalId}/jam',      [KehadiranController::class, 'updateJam'])->name('jam.update');
            Route::post('{jadwalId}/kehadiran', [KehadiranController::class, 'storeBulk'])->name('kehadiran.store');
            Route::get('{jadwalId}/kehadiran',  [KehadiranController::class, 'getByJadwal'])->name('kehadiran.get');
        });
    });

/*
|--------------------------------------------------------------------------
| KLN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:KLN'])
    ->prefix('kln')
    ->name('kln.')
    ->group(function () {

        Route::get('dashboard', [KlnController::class, 'index'])->name('dashboard');
        Route::view('profil',       'kln.profil')->name('profil');
        Route::view('jadwal',       'kln.jadwal')->name('jadwal');
        Route::view('announcement', 'kln.announcement')->name('announcement');
        Route::view('notifikasi',   'kln.notifikasi')->name('notifikasi');
        Route::view('analytics',    'kln.analytics')->name('analytics');

        // Jurusan management
        Route::prefix('jurusan')->name('jurusan.')->group(function () {
            Route::get('data',    [ApiJurusanController::class, 'getJurusan'])->name('data');
            Route::get('{id}',    [ApiJurusanController::class, 'showJurusan'])->name('show');
            Route::post('/',      [ApiJurusanController::class, 'storeJurusan'])->name('store');
            Route::patch('{id}',  [ApiJurusanController::class, 'updateJurusan'])->name('update');
            Route::delete('{id}', [ApiJurusanController::class, 'destroyJurusan'])->name('destroy');
        });

        // Dokumen
        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/',             [DokumenController::class, 'index'])->name('index');
            Route::post('/',            [DokumenController::class, 'store'])->name('store');
            Route::get('{id}/download', [DokumenController::class, 'download'])->name('download');
        });

        Route::patch('users/status/{id}', [KlnController::class, 'updateStatusMahasiswa'])->name('users.status.update');
    });

require __DIR__.'/auth.php';
