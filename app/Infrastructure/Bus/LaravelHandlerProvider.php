<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use Illuminate\Contracts\Container\Container;
use App\Application\Contracts\HandlerProviderInterface;

/**
 * Provider de handlers utilisant le container Laravel
 */
final class LaravelHandlerProvider implements HandlerProviderInterface
{
    public function __construct(
        private readonly Container $container
    ) {}

    /**
     * Crée une instance du handler via app()->make()
     */
    public function make(string $handlerClass): object
    {
        return $this->container->make($handlerClass);
    }
}
