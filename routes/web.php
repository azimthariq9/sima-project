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

            Route::get('jadwal',        [MahasiswaController::class, 'jadwal'])->name('jadwal');
            Route::get('announcement',  [MahasiswaController::class, 'announcement'])->name('announcement');
            Route::get('announcement/{id}', [MahasiswaController::class, 'announcementShow'])->name('announcement.show');
            Route::post('absensi/submit',   [MahasiswaController::class, 'submitAbsensi'])->name('absensi.submit');
            Route::get('notifikasi',          [MahasiswaController::class, 'notifikasi'])->name('notifikasi');
            Route::post('notifikasi/mark-read', [MahasiswaController::class, 'markNotifRead'])->name('notifikasi.mark-read');
            Route::get('analytics',     [MahasiswaController::class, 'analytics'])->name('analytics');
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
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('bipa',      [KlnController::class, 'jadwalBipa'])->name('bipa');
            Route::get('lecturers', [KlnController::class, 'jadwalLecturers'])->name('lecturers');
            Route::get('kln',       [KlnController::class, 'jadwalKln'])->name('kln');
            Route::get('kegiatan',  [KlnController::class, 'searchKegiatan'])->name('kegiatan');
            Route::get('{id}',      [KlnController::class, 'jadwalDetail'])->name('detail')->where('id', '[0-9]+');
            Route::post('/',        [KlnController::class, 'jadwalStore'])->name('store');
        });
        Route::get('announcement', [KlnController::class, 'announcementPage'])->name('announcement');
        Route::prefix('announcement')->name('announcement.')->group(function () {
            Route::get('create',                  [KlnController::class, 'createAnnouncementPage'])->name('create');
            Route::post('/',                      [KlnController::class, 'storeAnnouncement'])->name('store');
            Route::delete('file/{fileId}',        [KlnController::class, 'destroyAnnouncementFile'])->name('file.destroy');
            Route::get('{id}/edit',               [KlnController::class, 'editAnnouncementPage'])->name('edit');
            Route::post('{id}',                   [KlnController::class, 'updateAnnouncement'])->name('update');
            Route::delete('{id}',                 [KlnController::class, 'destroyAnnouncement'])->name('destroy');
            Route::get('{annId}/file/{fileId}',   [KlnController::class, 'serveAnnouncementFile'])->name('file');
        });
        Route::get('attendance',      [KlnController::class, 'attendancePage'])->name('attendance');
        Route::get('attendance/{id}', [KlnController::class, 'attendanceDetail'])->name('attendance.detail');
        Route::get('notifikasi',  [KlnController::class, 'notifikasiPage'])->name('notifikasi');
        Route::get('broadcast',   [KlnController::class, 'broadcastPage'])->name('broadcast');
        Route::post('broadcast',  [KlnController::class, 'storeBroadcast'])->name('broadcast.send');
        Route::view('analytics',    'kln.analytics')->name('analytics');

        // Users management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',       [KlnController::class, 'usersPage'])->name('page');
            Route::get('data',    [KlnController::class, 'getUsers'])->name('data');
            Route::get('{id}',    [KlnController::class, 'showUser'])->name('show');
            Route::post('/',      [KlnController::class, 'storeUser'])->name('store');
            Route::patch('{id}',  [KlnController::class, 'updateUser'])->name('update');
            Route::delete('{id}', [KlnController::class, 'destroyUser'])->name('destroy');
            Route::patch('status/{id}', [KlnController::class, 'updateStatusMahasiswa'])->name('status.update');
        });

        // Jurusan management
        Route::prefix('jurusan')->name('jurusan.')->group(function () {
            Route::get('data',    [ApiJurusanController::class, 'getJurusan'])->name('data');
            Route::get('{id}',    [ApiJurusanController::class, 'showJurusan'])->name('show');
            Route::post('/',      [ApiJurusanController::class, 'storeJurusan'])->name('store');
            Route::patch('{id}',  [ApiJurusanController::class, 'updateJurusan'])->name('update');
            Route::delete('{id}', [ApiJurusanController::class, 'destroyJurusan'])->name('destroy');
        });

        // Students & Lecturers
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/',                [KlnController::class, 'studentsPage'])->name('page');
            Route::get('mahasiswa/{id}',   [KlnController::class, 'mahasiswaDetail'])->name('mahasiswa');
            Route::get('dosen/{id}',       [KlnController::class, 'dosenDetail'])->name('dosen');
            Route::get('mahasiswa/{mahasiswaId}/dokumen/{dokumenId}/download', [KlnController::class, 'downloadDokumen'])->name('dokumen.download');
            Route::get('mahasiswa/{mahasiswaId}/dokumen/{dokumenId}/preview',  [KlnController::class, 'previewDokumen'])->name('dokumen.preview');
            Route::patch('mahasiswa/{mahasiswaId}/dokumen/{dokumenId}/status', [KlnController::class, 'updateDokumenStatus'])->name('dokumen.status');
        });

        // Dokumen
        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('page',            [KlnController::class, 'dokumen'])->name('page');
            Route::get('/',               [DokumenController::class, 'index'])->name('index');
            Route::post('/',              [DokumenController::class, 'store'])->name('store');
            Route::get('{id}',            [KlnController::class, 'show'])->name('show');
            Route::post('{id}/upload',    [KlnController::class, 'uploadFile'])->name('upload');
            Route::post('{id}/reject',    [KlnController::class, 'rejectDokumen'])->name('reject');
            Route::get('{id}/file',       [KlnController::class, 'downloadFile'])->name('file');
            Route::get('{id}/download',   [DokumenController::class, 'download'])->name('download');
            Route::delete('{id}',         [KlnController::class, 'destroy'])->name('destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| BIPA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:BIPA'])
    ->prefix('bipa')
    ->name('bipa.')
    ->group(function () {

        Route::get('dashboard',    [BipaController::class, 'dashboard'])->name('dashboard');
        Route::get('profil',       [BipaController::class, 'profil'])->name('profil');
        Route::get('notifikasi',   [BipaController::class, 'notifikasi'])->name('notifikasi');
        Route::get('announcement', [BipaController::class, 'announcement'])->name('announcement');
        Route::get('analytics',    [BipaController::class, 'analytics'])->name('analytics');
        Route::get('jadwal',       [BipaController::class, 'jadwal'])->name('jadwal');

        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/',            [BipaController::class, 'dokumen'])->name('index');
            Route::get('{id}',         [BipaController::class, 'showDokumen'])->name('show');
            Route::post('{id}/upload', [BipaController::class, 'uploadDokumen'])->name('upload');
        });

        Route::prefix('requestDok')->name('request.')->group(function () {
            Route::get('/',             [BipaController::class, 'indexReqDocument'])->name('index');
            Route::get('{id}',          [BipaController::class, 'showReqDocument'])->name('show');
            Route::patch('{id}/status', [BipaController::class, 'updateReqDokumen'])->name('status');
            Route::post('{id}/upload',  [BipaController::class, 'uploadReqDokumen'])->name('upload');
        });
    });

require __DIR__.'/auth.php';
