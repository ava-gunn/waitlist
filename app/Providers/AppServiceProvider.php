<?php

namespace App\Providers;

use App\Repositories\ProjectRepository;
use App\Repositories\SignupRepository;
use App\Repositories\WaitlistTemplateRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectRepository::class, function () {
            return new ProjectRepository;
        });

        $this->app->singleton(SignupRepository::class, function () {
            return new SignupRepository;
        });

        $this->app->singleton(WaitlistTemplateRepository::class, function () {
            return new WaitlistTemplateRepository;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default app.domain for tests
        if (! config('app.domain')) {
            config(['app.domain' => 'waitlist.test']);
        }
    }
}
