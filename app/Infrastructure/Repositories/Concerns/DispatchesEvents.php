<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Concerns;

use Log;
use App\Jobs\ProcessDomainEvent;
use Illuminate\Support\Facades\Event;
use App\Domain\Shared\Entities\AggregateRoot;

trait DispatchesEvents
{
    /**
     * Dispatch tous les events d'un aggregate après persistence
     * Films/Cinémas/Salles/Séances → ASYNC (queue mongodb-sync)
     * Réservations → SYNC (direct)
     */
    protected function dispatchDomainEvents(AggregateRoot $aggregate): void
    {
        $events = $aggregate->getDomainEvents();

        foreach ($events as $event) {
            if ($event->isQueueEvent()) {
                // ASYNC - Dispatch via Job Laravel propre
                ProcessDomainEvent::dispatch($event);

                Log::info('Event dispatched to ASYNC queue via Job', [
                    'event'        => $event->getEventName(),
                    'aggregate_id' => $event->getAggregateId(),
                    'queue'        => 'mongodb-sync',
                    'job'          => 'ProcessDomainEvent',
                ]);
            } else {
                // SYNC - Dispatch direct (réservations)
                Event::dispatch($event);

                Log::info('Event dispatched SYNCHRONOUSLY', [
                    'event'        => $event->getEventName(),
                    'aggregate_id' => $event->getAggregateId(),
                    'mode'         => 'sync',
                ]);
            }
        }

        $aggregate->clearDomainEvents();
    }
}
