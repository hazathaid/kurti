<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KurtiController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kurtis', [KurtiController::class, 'index'])->name('kurtis.index');
    Route::get('/kurtis/{murid}/{pekan}', [KurtiController::class, 'show'])->name('kurtis.show');
    Route::put('/kurtis/{id}/catatan', [KurtiController::class, 'updateCatatan'])->name('kurtis.updateCatatan');
});

require __DIR__.'/auth.php';
