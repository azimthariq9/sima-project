<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MahasiswaRequestController;
use App\Http\Controllers\API\KlnController;
use App\Http\Controllers\API\DokumenController;
use App\Http\Controllers\API\MahasiswaController;


/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth Custom
|--------------------------------------------------------------------------
*/
Route::post('login1', [AuthenticatedSessionController::class, 'store'])->name('login1');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Global Dashboard (Role Redirect Logic)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Global Profile (Generic Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| KLN / ADMIN ROUTES
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'check.role:KLN'])->group(function () {

//     Route::apiResource('users', UserController::class);

// });

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES
|--------------------------------------------------------------------------
*/

$roles = [
    'mahasiswa' => 'MAHASISWA',
    'kln'       => 'KLN',
    'dosen'     => 'DOSEN',
    'bipa'      => 'BIPA',
    'jurusan'   => 'JURUSAN',
];

foreach ($roles as $prefix => $enumName) {

    Route::middleware(['auth', "check.role:$enumName"])
        ->prefix($prefix)
        ->name($prefix . '.')
        ->group(function () use ($prefix) {

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', fn () => view("$prefix.dashboard"))
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Common Pages (Auto)
            |--------------------------------------------------------------------------
            */
            Route::get('/profil', fn () => view("$prefix.profil"))
                ->name('profil');

            Route::get('/notifikasi', fn () => view("$prefix.notifikasi"))
                ->name('notifikasi');

            Route::get('/announcement', fn () => view("$prefix.announcement"))
                ->name('announcement');

            Route::get('/jadwal', fn () => view("$prefix.jadwal"))
                ->name('jadwal');

            Route::get('/analytics', fn () => view("$prefix.analytics"))
                ->name('analytics');
        });
}

/*
|--------------------------------------------------------------------------
| MAHASISWA EXTRA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:MAHASISWA'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('request', [MahasiswaRequestController::class, 'index'])
            ->name('request.index');

        Route::get('request/create', [MahasiswaRequestController::class, 'create'])
            ->name('request.create');

        Route::post('request', [MahasiswaRequestController::class, 'store'])
            ->name('request.store');

        
    });

    // Mahasiswa routes
Route::middleware(['auth', 'check.role:MAHASISWA'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Profile
    Route::get('profile', [MahasiswaController::class, 'getProfile'])->name('profile');
    Route::patch('profile', [MahasiswaController::class, 'updateProfile'])->name('profile.update');
    
    // Dashboard
    Route::get('dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    // Dokumen routes
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [DokumenController::class, 'index'])->name('index');
        Route::post('/', [DokumenController::class, 'store'])->name('store');
        Route::get('{id}/download', [DokumenController::class, 'download'])->name('download');
        
    });
     // Request Dokumen
    Route::prefix('requestDok')->name('request.')->group(function () {
        Route::get('/', [MahasiswaController::class, 'getRequestDokumen'])->name('index');
        Route::post('/', [MahasiswaController::class, 'requestDokumen'])->name('store');
    });

});

/*
|--------------------------------------------------------------------------
| KLN EXTRA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:KLN'])
    ->prefix('kln')
    ->name('kln.')
    ->group(function () {

        Route::get('/monitoring', fn () => view('kln.monitoring'))
            ->name('monitoring');

        Route::get('/validasi', fn () => view('kln.validasi'))
            ->name('validasi');

        Route::get('/schedule', fn () => view('kln.schedule'))
            ->name('schedule');

        Route::post('/announcement/store', fn () =>
            back()->with('success', 'Pengumuman berhasil dikirim')
        )->name('announcement.store');

        Route::get('/users', [KlnController::class, 'usersPage'])
            ->name('users.page');

        Route::get('/dokumen', [KlnController::class, 'dokumenPage'])
            ->name('dokumen.page');

        /*
        |--------------------------------------------------------------------------
        | KLN API
        |--------------------------------------------------------------------------
        */
        Route::prefix('api')->name('api.')->group(function () {

            Route::get('dashboard/stats', [KlnController::class, 'getDashboardStats'])
                ->name('dashboard.stats');

            Route::get('users', [KlnController::class, 'getUsers'])
                ->name('users.index');

            Route::post('users', [KlnController::class, 'storeUser'])
                ->name('users.store');

            Route::get('users/{id}', [KlnController::class, 'showUser'])
                ->name('users.show');

            Route::put('users/{id}', [KlnController::class, 'updateUser'])
                ->name('users.update');

            Route::delete('users/{id}', [KlnController::class, 'destroyUser'])
                ->name('users.destroy');

            Route::put('users/{id}/status', [KlnController::class, 'updateStatusMahasiswa'])
                ->name('users.status');

            Route::get('dokumen', [KlnController::class, 'getDokumen'])
                ->name('dokumen.index');

            Route::put('dokumen/{id}/status', [KlnController::class, 'updateDokumenStatus'])
                ->name('dokumen.status');

            //Request Dokumen
            Route::prefix('requestDok')->name('request.')->group(function () {
                Route::get('/', [KlnController::class, 'indexReqDocument'])->name('index');
                // Route::get('stats', [KlnController::class, 'getStats'])->name('stats');
                Route::get('{id}', [KlnController::class, 'showReqDocument'])->name('show');
                Route::patch('{id}/status', [KlnController::class, 'updateReqDokumen'])->name('status');
                Route::post('{id}/upload', [KlnController::class, 'uploadReqDokumen'])->name('upload');
        });
        });
    });

/*
|--------------------------------------------------------------------------
| Breeze Auth
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:KLN'])->prefix('kln')->name('kln.')->group(function () {
    
    // ===== HALAMAN (return view) =====
    Route::get('dashboard', [KlnController::class, 'index'])->name('dashboard');
    Route::get('users', [KlnController::class, 'usersPage'])->name('users.page');
    Route::get('dokumen', [KlnController::class, 'dokumenPage'])->name('dokumen.page');
    
    // ===== API INTERNAL (return JSON) =====
    Route::prefix('api')->name('api.')->group(function () {
        // Dashboard
        Route::get('dashboard/stats', [KlnController::class, 'getDashboardStats'])->name('dashboard.stats');
        //Announcement
        Route::post('coba-announcement', [KlnController::class, 'storeAnnouncement'])->name('announcement.store');
        // Users
        Route::get('users', [KlnController::class, 'getUsers'])->name('users.index');
        Route::post('users', [KlnController::class, 'storeUser'])->name('users.store');
        Route::get('users/{id}', [KlnController::class, 'showUser'])->name('users.show');
        Route::put('users/{id}', [KlnController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{id}', [KlnController::class, 'destroyUser'])->name('users.destroy');
        Route::post('test-announcement', function() {
            return response()->json([
                'message' => 'Test route works',
                'user' => auth()->user()
            ]);
        })->name('test.announcement');
        // Dokumen
        Route::get('dokumen', [KlnController::class, 'getDokumen'])->name('dokumen.index');
        Route::patch('dokumen/{id}/status', [KlnController::class, 'updateDokumenStatus'])->name('dokumen.status');
        
        // Jadwal
        Route::apiResource('jadwal', KlnController::class)->only(['index', 'store', 'update', 'destroy']);

        
    });
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
require __DIR__.'/auth.php';