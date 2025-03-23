<?php

namespace Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class FeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fix risky test warnings in Laravel 12
        $this->withoutExceptionHandling([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            NotFoundHttpException::class,
        ]);

        // Prevent facades from handling exceptions during tests
        Facade::clearResolvedInstances();
    }

    /**
     * Don't handle given exceptions during test execution
     */
    protected function withoutExceptionHandling(array $except = []): static
    {
        $this->app->instance(ExceptionHandler::class, new class($this->app->make(ExceptionHandler::class), $except) extends Handler
        {
            protected $except;
            protected $originalHandler;

            public function __construct($originalHandler, $except)
            {
                $this->originalHandler = $originalHandler;
                $this->except = $except;
                parent::__construct($originalHandler->container);
            }

            public function render($request, Throwable $e)
            {
                if ($this->shouldReport($e) && ! $this->isExempt($e)) {
                    throw $e;
                }

                return $this->originalHandler->render($request, $e);
            }

            public function report(Throwable $e)
            {
                if (! $this->isExempt($e)) {
                    throw $e;
                }

                return $this->originalHandler->report($e);
            }

            protected function isExempt(Throwable $e): bool
            {
                foreach ($this->except as $exempt) {
                    if ($e instanceof $exempt) {
                        return true;
                    }
                }

                return false;
            }
        });

        return $this;
    }
}
