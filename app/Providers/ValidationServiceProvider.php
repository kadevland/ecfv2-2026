<?php

declare(strict_types=1);

namespace App\Providers;

use Respect\Validation\Factory;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure the Factory to load custom rules from our namespace
        Factory::setDefaultInstance(
            (new Factory)
                ->withRuleNamespace('App\\Domain\\Shared\\Validation\\Rules')
                ->withExceptionNamespace('App\\Domain\\Shared\\Validation\\Exceptions')
        );
    }
}
