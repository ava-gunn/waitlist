<?php

use App\Http\Controllers\Projects\SignupsController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

// Public signup routes (no auth required)
Route::post('signup/{subdomain}', [SignupController::class, 'store'])->name('signup.store');
Route::get('verify/{token}', [SignupController::class, 'verify'])->name('signup.verify');

// Auth protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('projects/{project}/signups', [SignupsController::class, 'index'])->name('projects.signups.index');
    Route::get('projects/{project}/signups/export', [SignupsController::class, 'export'])->name('projects.signups.export');
    Route::delete('projects/{project}/signups/{signup}', [SignupsController::class, 'destroy'])->name('projects.signups.destroy');
});
