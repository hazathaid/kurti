<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KurtiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kurtis', [KurtiController::class, 'index'])->name('kurtis.index');
    Route::get('/kurti/{murid}/{group}', [KurtiController::class, 'show'])->name('kurtis.show');
    Route::put('/kurtis/{id}/catatan', [KurtiController::class, 'updateCatatan'])->name('kurtis.updateCatatan');
    Route::get('/kurtis/{murid}/create', [KurtiController::class, 'create'])->name('kurtis.create');
    Route::post('/kurtis', [KurtiController::class, 'store'])->name('kurtis.store');
    Route::get('/kurtis/{kurti}/edit', [KurtiController::class, 'edit'])->name('kurtis.edit');
    Route::put('/kurtis/{kurti}', [KurtiController::class, 'update'])->name('kurtis.update');
    Route::delete('/kurtis/{kurti}', [KurtiController::class, 'destroy'])->name('kurtis.destroy');
});

require __DIR__.'/auth.php';
