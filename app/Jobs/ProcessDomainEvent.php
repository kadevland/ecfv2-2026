<?php

declare(strict_types=1);

namespace App\Jobs;

use Log;
use Illuminate\Support\Facades\Event;
use App\Domain\Shared\Events\DomainEvent;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Job Laravel pour traiter les events de domaine en mode asynchrone
 */
class ProcessDomainEvent implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly DomainEvent $event
    ) {
        $this->onQueue('mongodb-sync');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Processing domain event in job', [
            'event'        => $this->event->getEventName(),
            'aggregate_id' => $this->event->getAggregateId(),
            'job'          => 'ProcessDomainEvent',
        ]);

        Event::dispatch($this->event);

        Log::info('Domain event processed successfully', [
            'event'        => $this->event->getEventName(),
            'aggregate_id' => $this->event->getAggregateId(),
        ]);
    }
}
