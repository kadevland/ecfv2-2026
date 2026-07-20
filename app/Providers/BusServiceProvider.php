<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Bus\QueryBus;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryRegistry;
use App\Application\Bus\PublicQueryBus;
use Illuminate\Support\ServiceProvider;
use App\Application\Bus\CommandRegistry;
use Illuminate\Contracts\Container\Container;
use App\Infrastructure\Bus\LaravelHandlerProvider;
use App\Application\Contracts\HandlerProviderInterface;

/**
 * Service Provider pour les Bus CQRS
 */
class BusServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->registerHandlerProvider();
        $this->registerRegistries();
        $this->registerBuses();
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        $this->registerHandlers();
    }

    /**
     * Register Handler Provider
     */
    private function registerHandlerProvider(): void
    {
        $this->app->singleton(HandlerProviderInterface::class, LaravelHandlerProvider::class);
    }

    /**
     * Register Registries
     */
    private function registerRegistries(): void
    {
        $this->app->singleton(CommandRegistry::class);
        $this->app->singleton(QueryRegistry::class);
    }

    /**
     * Register Buses
     */
    private function registerBuses(): void
    {
        $this->app->singleton(CommandBus::class, function (Container $app) {
            return new CommandBus(
                $app->make(CommandRegistry::class),
                $app->make(HandlerProviderInterface::class)
            );
        });

        $this->app->singleton(QueryBus::class, function (Container $app) {
            return new QueryBus(
                $app->make(QueryRegistry::class),
                $app->make(HandlerProviderInterface::class)
            );
        });

        // Bus dédié pour les queries publiques (MongoDB)
        $this->app->singleton(PublicQueryBus::class, function (Container $app) {
            return new PublicQueryBus;
        });
    }

    /**
     * Enregistre les handlers dans les registries
     */
    private function registerHandlers(): void
    {
        $commandRegistry = $this->app->make(CommandRegistry::class);
        $queryRegistry   = $this->app->make(QueryRegistry::class);

        // Enregistrement des mappings depuis la configuration
        $commandMappings = config('bus.command_mappings', []);
        if (!empty($commandMappings)) {
            $commandRegistry->registerMany($commandMappings);
        }

        $queryMappings = config('bus.query_mappings', []);
        if (!empty($queryMappings)) {
            $queryRegistry->registerMany($queryMappings);
        }
    }
}
