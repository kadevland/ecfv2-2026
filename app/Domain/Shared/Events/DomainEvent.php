<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

use DateTime;
use Ramsey\Uuid\Uuid;
use DateTimeInterface;

/**
 * Classe de base pour tous les événements du domaine
 *
 * Fournit une implémentation par défaut des méthodes communes
 */
abstract class DomainEvent implements DomainEventInterface
{
    protected readonly string $eventId;

    protected readonly DateTimeInterface $occurredOn;

    public function __construct()
    {
        $this->eventId    = Uuid::uuid4()->toString();
        $this->occurredOn = new DateTime;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getOccurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }

    /**
     * Détermine si cet event doit être traité en mode queue (async) ou sync
     * Par défaut: queue (async) - override pour sync si nécessaire
     */
    public function isQueueEvent(): bool
    {
        return true; // Par défaut ASYNC
    }

    /**
     * Les sous-classes doivent implémenter ces méthodes
     */
    abstract public function getEventName(): string;

    abstract public function getAggregateId(): string;

    abstract public function getAggregateType(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
