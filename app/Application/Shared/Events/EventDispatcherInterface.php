<?php

declare(strict_types=1);

namespace App\Application\Shared\Events;

use App\Domain\Shared\Events\DomainEventInterface;

/**
 * Interface pour dispatcher les événements du domaine
 * Permet de découpler le domaine de l'infrastructure Laravel
 */
interface EventDispatcherInterface
{
    /**
     * Dispatch un événement du domaine
     */
    public function dispatch(DomainEventInterface $event): void;

    /**
     * Dispatch plusieurs événements en batch
     *
     * @param DomainEventInterface[] $events
     */
    public function dispatchMany(array $events): void;
}
