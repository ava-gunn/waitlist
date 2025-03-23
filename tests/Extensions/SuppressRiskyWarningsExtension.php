<?php

namespace Tests\Extensions;

use PHPUnit\Event\Test\PreConditionCalled;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * PHPUnit Extension to suppress risky test warnings in Laravel 12.
 */
class SuppressRiskyWarningsExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        // Register a hook that runs before a test
        $facade->registerSubscriber(new PreConditionCalled(
            function (PreConditionCalled $event): void {
                // Get the test case instance
                $test = $event->test();
                if ($test instanceof TestCase) {
                    // Set properties to prevent risky test warnings
                    $test->backupGlobals = false;
                    $test->backupStaticAttributes = false;
                }
            }
        ));
    }
}
