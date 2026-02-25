<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::post('login1',[AuthenticatedSessionController::class, 'store'])->name('login1');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified', 'check.role:KLN,adminBipa'])->name('dashboard');

use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});
    
Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('check.role:KLN')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});

Route::get('test', [UserController::class, 'index']);


require __DIR__.'/auth.php';
