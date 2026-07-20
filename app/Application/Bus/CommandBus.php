<?php

declare(strict_types=1);

namespace App\Application\Bus;

use InvalidArgumentException;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Application\Contracts\HandlerProviderInterface;

/**
 * Command Bus simple
 *
 * Dispatche les commands vers leurs handlers via un registry
 */
final class CommandBus
{
    public function __construct(
        private readonly CommandRegistry $registry,
        private readonly HandlerProviderInterface $handlerProvider
    ) {}

    /**
     * Dispatch a command to its handler(s)
     */
    public function dispatch(CommandInterface $command): mixed
    {
        $commandClass = get_class($command);

        // Récupère les handlers depuis le registry
        $handlerClasses = $this->registry->getHandlers($commandClass);

        $results = [];

        // Exécute tous les handlers
        foreach ($handlerClasses as $handlerClass) {
            // Crée l'instance du handler
            $handler = $this->handlerProvider->make($handlerClass);

            // dd($handler);

            if (!$handler instanceof CommandHandlerInterface) {
                throw new InvalidArgumentException(
                    "Handler [{$handlerClass}] must implement CommandHandlerInterface"
                );
            }

            $results[] = $handler->handle($command);
        }

        // Retourne le dernier résultat ou null si aucun handler
        return end($results) ?: null;
    }
}
