<?php

declare(strict_types=1);

namespace App\Application\Bus;

use InvalidArgumentException;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Contracts\HandlerProviderInterface;

/**
 * Query Bus simple
 *
 * Dispatche les queries vers leurs handlers via un registry
 */
final class QueryBus
{
    public function __construct(
        private readonly QueryRegistry $registry,
        private readonly HandlerProviderInterface $handlerProvider
    ) {}

    /**
     * Dispatch a query to its handler(s)
     */
    public function ask(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);

        // Récupère les handlers depuis le registry
        $handlerClasses = $this->registry->getHandlers($queryClass);

        $results = [];

        // Exécute tous les handlers
        foreach ($handlerClasses as $handlerClass) {
            // Crée l'instance du handler
            $handler = $this->handlerProvider->make($handlerClass);

            if (!$handler instanceof QueryHandlerInterface) {
                throw new InvalidArgumentException(
                    "Handler [{$handlerClass}] must implement QueryHandlerInterface"
                );
            }

            $results[] = $handler->handle($query);
        }

        // Retourne le dernier résultat ou null si aucun handler
        return end($results) ?: null;
    }
}
