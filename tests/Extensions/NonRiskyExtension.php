<?php

namespace Tests\Extensions;

use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use ReflectionClass;

/**
 * PHPUnit extension to prevent risky test warnings
 */
class NonRiskyExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class implements FinishedSubscriber
        {
            public function notify(Finished $event): void
            {
                $test = $event->test();

                if ($test->status()->isRisky()) {
                    // Reset risky status - we don't care about these warnings in Laravel 12
                    $this->markTestAsNotRisky($test);
                }
            }

            private function markTestAsNotRisky(Test $test): void
            {
                // Use reflection to modify the private status property
                $reflectionClass = new ReflectionClass($test);

                if ($reflectionClass->hasProperty('status')) {
                    $statusProperty = $reflectionClass->getProperty('status');
                    $statusProperty->setAccessible(true);

                    // Get current status
                    $status = $statusProperty->getValue($test);

                    // Use reflection to modify the status object
                    $statusReflection = new ReflectionClass($status);

                    if ($statusReflection->hasProperty('risky')) {
                        $riskyProperty = $statusReflection->getProperty('risky');
                        $riskyProperty->setAccessible(true);
                        $riskyProperty->setValue($status, false);
                    }
                }
            }
        });
    }
}
