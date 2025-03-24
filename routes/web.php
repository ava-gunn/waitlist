<?php

use App\Http\Controllers\WaitlistLandingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Subdomain routes for accessing waitlist landing pages
Route::domain('{subdomain}.' . config('app.domain'))->group(function () {
    Route::get('/', [WaitlistLandingController::class, 'show'])->name('waitlist.landing');
    Route::post('signup', function (string $subdomain) {
        return app()->call([app(\App\Http\Controllers\SignupController::class), 'store'], ['subdomain' => $subdomain]);
    })->name('waitlist.signup');
});

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        $projects = $user->projects()->with('signups')->withCount('signups')->latest()->take(5)->get();

        return Inertia::render('dashboard', [
            'recentProjects' => $projects,
        ]);
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/projects.php';
require __DIR__ . '/signups.php';
require __DIR__ . '/templates.php';
