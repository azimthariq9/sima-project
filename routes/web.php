<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| HOME (Fix AdminLTE Route [home] not defined)
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

/*
|--------------------------------------------------------------------------
| ROLE BASED DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'admin'      => redirect()->route('admin.dashboard'),
        'mahasiswa'  => redirect()->route('mahasiswa.dashboard'),
        'kln'        => redirect()->route('kln.dashboard'),
        'jurusan'    => redirect()->route('jurusan.dashboard'),
        'bipa'       => redirect()->route('bipa.dashboard'),
        default      => abort(403),
    };

})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});

/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MahasiswaRequestController;

Route::middleware(['auth'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/request', [MahasiswaRequestController::class, 'index'])
            ->name('request.index');

        Route::get('/request/create', [MahasiswaRequestController::class, 'create'])
            ->name('request.create');

        Route::get('/profil', [MahasiswaController::class, 'profil'])
            ->name('profil');

        Route::get('/profil/dokumen', [MahasiswaController::class, 'dokumen'])
            ->name('profil.dokumen');

        Route::get('/jadwal', [MahasiswaController::class, 'jadwal'])
            ->name('jadwal');

        Route::get('/notifikasi', [MahasiswaController::class, 'notifikasi'])
            ->name('notifikasi');

        Route::get('/announcement', [MahasiswaController::class, 'announcement'])
            ->name('announcement');

        Route::get('/announcement/{id}', [MahasiswaController::class, 'announcementShow'])
            ->name('announcement.show');

        Route::get('/analytics', [MahasiswaController::class, 'analytics'])
            ->name('analytics');

    });


/*
|--------------------------------------------------------------------------
| KLN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/kln/dashboard', function () {
        return view('kln.dashboard');
    })->name('kln.dashboard');

});

/*
|--------------------------------------------------------------------------
| JURUSAN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/jurusan/dashboard', function () {
        return view('jurusan.dashboard');
    })->name('jurusan.dashboard');

});

/*
|--------------------------------------------------------------------------
| BIPA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/bipa/dashboard', function () {
        return view('bipa.dashboard');
    })->name('bipa.dashboard');

});

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
