<?php

use App\Http\Controllers\WaitlistTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Global template routes
    Route::get('templates', [WaitlistTemplateController::class, 'index'])->name('templates.index');
});
