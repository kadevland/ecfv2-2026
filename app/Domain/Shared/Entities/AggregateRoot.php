<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entities;

use App\Domain\Shared\Events\DomainEvent;

/**
 * Base class for Domain Aggregate Roots in DDD
 */
abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    /**
     * Get all domain events
     *
     * @return DomainEvent[]
     */
    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    /**
     * Clear all domain events
     */
    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    /**
     * Add a domain event to be dispatched later
     * If event of same type already exists, replace it with newest data
     */
    protected function addDomainEvent(DomainEvent $event): void
    {
        $eventType = $event->getEventName();

        // Check if event of same type already exists
        foreach ($this->domainEvents as $index => $existingEvent) {
            if ($existingEvent->getEventName() === $eventType) {
                // Replace with newest event (contains latest data)
                $this->domainEvents[$index] = $event;

                return;
            }
        }

        // No existing event of this type, add it
        $this->domainEvents[] = $event;
    }
}
