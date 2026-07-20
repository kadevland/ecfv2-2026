<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la création d'un cinéma
 */
final class CinemaCreated extends DomainEvent
{
    private function __construct(
        private readonly string $cinemaUuid
    ) {
        parent::__construct();
    }

    public static function fromCinema(Cinema $cinema): self
    {
        return new self($cinema->id->value);
    }

    public function getEventName(): string
    {
        return 'cinema.cinema.created';
    }

    public function getAggregateId(): string
    {
        return $this->cinemaUuid;
    }

    public function getAggregateType(): string
    {
        return 'cinema';
    }

    public function getCinemaUuid(): string
    {
        return $this->cinemaUuid;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cinema_uuid' => $this->cinemaUuid,
            'event_type'  => 'created',
        ];
    }
}
