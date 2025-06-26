<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ClockingController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Form routes - protected by authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
    
    // Clocking data routes
    Route::get('/api/clocking', [ClockingController::class, 'index'])->name('clocking.index');
    Route::post('/api/clocking', [ClockingController::class, 'store'])->name('clocking.store');
    Route::put('/api/clocking/{clocking}', [ClockingController::class, 'update'])->name('clocking.update');
    Route::delete('/api/clocking/{clocking}', [ClockingController::class, 'destroy'])->name('clocking.destroy');
    Route::get('/api/dashboard-stats', [ClockingController::class, 'getDashboardStats'])->name('clocking.dashboard-stats');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
