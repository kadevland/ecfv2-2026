<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Seance;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la mise à jour d'une séance
 */
final class SeanceUpdated extends DomainEvent
{
    private function __construct(
        private readonly string $seanceUuid
    ) {
        parent::__construct();
    }

    public static function fromSeance(Seance $seance): self
    {
        return new self($seance->id->value);
    }

    public static function fromUuid(string $seanceUuid): self
    {
        return new self($seanceUuid);
    }

    public function getEventName(): string
    {
        return 'cinema.seance.updated';
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
            'event_type'  => 'updated',
        ];
    }
}
