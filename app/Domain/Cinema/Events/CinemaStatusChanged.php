<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\CinemaId;

/**
 * Événement déclenché lors du changement de statut d'un cinéma
 */
final class CinemaStatusChanged extends DomainEvent
{
    private function __construct(
        private readonly string $cinemaUuid
    ) {
        parent::__construct();
    }

    public static function fromCinemaId(CinemaId $cinemaId): self
    {
        return new self($cinemaId->value);
    }

    public function getEventName(): string
    {
        return 'cinema.cinema.status_changed';
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
            'event_type'  => 'status_changed',
        ];
    }
}
