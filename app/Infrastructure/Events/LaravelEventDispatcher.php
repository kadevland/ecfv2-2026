<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use Illuminate\Contracts\Events\Dispatcher;
use App\Domain\Shared\Events\DomainEventInterface;
use App\Application\Shared\Events\EventDispatcherInterface;

/**
 * Implémentation Laravel de l'EventDispatcher
 *
 * Bridge entre les événements du domaine et le système d'événements Laravel
 */
final readonly class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private Dispatcher $laravelEventDispatcher
    ) {}

    public function dispatch(DomainEventInterface $event): void
    {
        // Dispatch l'événement dans le système Laravel
        $this->laravelEventDispatcher->dispatch($event->getEventName(), [
            'event'         => $event,
            'eventId'       => $event->getEventId(),
            'aggregateId'   => $event->getAggregateId(),
            'aggregateType' => $event->getAggregateType(),
            'occurredOn'    => $event->getOccurredOn(),
            'data'          => $event->toArray(),
        ]);

        // Log pour debug si nécessaire
        logger()->debug('Domain event dispatched', [
            'event_name'     => $event->getEventName(),
            'event_id'       => $event->getEventId(),
            'aggregate_id'   => $event->getAggregateId(),
            'aggregate_type' => $event->getAggregateType(),
        ]);
    }

    public function dispatchMany(array $events): void
    {
        foreach ($events as $event) {
            if ($event instanceof DomainEventInterface) {
                $this->dispatch($event);
            }
        }
    }
}
