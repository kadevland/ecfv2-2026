<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\SeanceId;

/**
 * Événement déclenché lors de la suppression d'une séance
 */
final class SeanceDeleted extends DomainEvent
{
    private function __construct(
        private readonly string $seanceUuid
    ) {
        parent::__construct();
    }

    public static function fromSeanceId(SeanceId $seanceId): self
    {
        return new self($seanceId->value);
    }

    public function getEventName(): string
    {
        return 'cinema.seance.deleted';
    }

    public function getAggregateId(): string
    {
        return $this->seanceUuid;
    }

    public function getAggregateType(): string
    {
        return 'seance';
    }

    public function getSeanceUuid(): string
    {
        return $this->seanceUuid;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'seance_uuid' => $this->seanceUuid,
            'event_type'  => 'deleted',
        ];
    }
}
