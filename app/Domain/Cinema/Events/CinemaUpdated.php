<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la mise à jour d'un cinéma
 */
final class CinemaUpdated extends DomainEvent
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

    public static function fromUuid(string $cinemaUuid): self
    {
        return new self($cinemaUuid);
    }

    public function getEventName(): string
    {
        return 'cinema.cinema.updated';
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
            'event_type'  => 'updated',
        ];
    }
}
