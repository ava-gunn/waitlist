<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Projects\TemplatesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Projects
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Projects and Templates
    Route::prefix('projects/{project}')->group(function () {
        // Templates
        Route::get('templates', [TemplatesController::class, 'index'])->name('projects.templates.index');
        Route::get('templates/{template}/edit', [TemplatesController::class, 'edit'])->name('projects.templates.edit');
        Route::patch('templates/{template}', [TemplatesController::class, 'update'])->name('projects.templates.update');
        Route::post('templates/{template}/activate', [TemplatesController::class, 'activate'])->name('projects.templates.activate');
        Route::post('templates/{template}/deactivate', [TemplatesController::class, 'deactivate'])->name('projects.templates.deactivate');
    });
});
