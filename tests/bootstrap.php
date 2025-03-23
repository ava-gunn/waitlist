<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\Helpers\InertiaTestCase;
use Tests\Helpers\ViteManifestFake;

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Run the database migrations
Artisan::call('migrate:fresh');

// Create fake Vite manifest for tests
ViteManifestFake::create();

// Setup Inertia for testing
InertiaTestCase::mockInertiaRendering();

// Fix risky test warnings by preserving the default exception handler
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

// Mock HTTP responses for external services to avoid real API calls
Http::preventStrayRequests();

// Register a teardown callback to clean up the fake manifest after tests
register_shutdown_function(function () {
    ViteManifestFake::delete();
});

return $app;
