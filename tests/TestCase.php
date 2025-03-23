<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Suppress the PHPUnit warning about not preserving global state
        $this->backupGlobals = false;
        $this->runTestInSeparateProcess = false;
    }

    /**
     * Handle exceptional errors in the test.
     *
     * This suppresses the "removed error handlers" warnings in Laravel 12.
     *
     * @param  Throwable  $e
     * @param  bool  $isDuringTest
     */
    protected function handleExceptionalError($e, $isDuringTest = false)
    {
        if ($isDuringTest) {
            throw $e;
        }

        parent::handleExceptionalError($e, $isDuringTest);
    }
}
