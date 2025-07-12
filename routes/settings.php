<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\UserManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    // Replace the existing user-management route with this:
    Route::get('settings/user-management', [UserManagementController::class, 'index'])
        ->name('user-management');
    // Add these new routes for user management
    Route::post('settings/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('settings/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('settings/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
