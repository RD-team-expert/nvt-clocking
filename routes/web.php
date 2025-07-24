<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ClockingController;
use App\Http\Controllers\ExportingController;
use App\Http\Controllers\TemplateController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/export/{model}/csv/{start?}/{end?}', [ExportingController::class, 'ExportClockingCsv']);
Route::get('/export/{model}/json/{start?}/{end?}', [ExportingController::class, 'ExportClockingJson']);



// Form routes - protected by authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index')->middleware('can:forms.index');;
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create')->middleware('can:forms.create');;
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store')->middleware('can:forms.create');;
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit')->middleware('can:forms.edit');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update')->middleware('can:forms.edit');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy')->middleware('can:forms.destroy');

    // Clocking records routes
    Route::get('/api/clocking', [ClockingController::class, 'index'])->name('clocking.index');
    Route::post('/api/clocking', [ClockingController::class, 'store'])->name('clocking.store');
    Route::put('/api/clocking/{clocking}', [ClockingController::class, 'update'])->name('clocking.update');

    Route::get('/api/dashboard-stats', [ClockingController::class, 'getDashboardStats'])->name('clocking.dashboard-stats');

    // Route::post('/create/clocking/{clocking}', [Clockinoutdatacontroller::class, 'create'])->name('clocking.create');

    Route::delete('/clocking/{clocking}', [ClockingController::class, 'destroy'])->name('clocking.destroy');

    Route::get('/forms/download', [TemplateController::class, 'downloadCsv'])
     ->name('forms.downloadCsv')->middleware('can:forms.edit');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
