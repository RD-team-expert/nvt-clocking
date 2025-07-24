<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\UserManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    // Replace the existing user-management route with this:
    Route::get('settings/user-management', [UserManagementController::class, 'index'])
        ->name('user-management')->middleware('permission:roles.index|users.index');
    // Add these new routes for user management
    Route::post('settings/users', [UserManagementController::class, 'store'])->name('users.store')->middleware('can:users.create');
    Route::put('settings/users/{id}', [UserManagementController::class, 'update'])->name('users.update')->middleware('can:users.edit');
    Route::delete('settings/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('can:users.destroy');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance',['permissions' => Auth::user()->getAllPermissions(),]);
    })->name('appearance');

    Route::post('/roles', [UserManagementController::class, 'storeRole'])->name('roles.store')->middleware('can:roles.create');
    Route::put('/roles/{role}', [UserManagementController::class, 'updateRole'])->name('roles.update')->middleware('can:roles.edit');
    Route::delete('/roles/{role}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy')->middleware('can:roles.destroy');
});
