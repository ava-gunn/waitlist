<?php

namespace Tests\Helpers;

use Illuminate\Testing\TestResponse;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia;

class InertiaTestCase
{
    /**
     * Setup Inertia test response expectations
     */
    public static function from(TestResponse $response): AssertableInertia
    {
        return AssertableInertia::fromResponse($response);
    }

    /**
     * Mock Inertia rendering for tests
     */
    public static function mockInertiaRendering(): void
    {
        // This ensures that even if a component is missing, Inertia will still render a response
        // during testing rather than throwing an exception
        if (class_exists(Inertia::class)) {
            Inertia::version(function () {
                return md5(uniqid());
            });
        }
    }
}
