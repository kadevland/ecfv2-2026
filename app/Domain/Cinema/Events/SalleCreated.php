<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Salle;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la création d'une salle
 */
final class SalleCreated extends DomainEvent
{
    private function __construct(
        private readonly string $salleUuid
    ) {
        parent::__construct();
    }

    public static function fromSalle(Salle $salle): self
    {
        return new self($salle->id->value);
    }

    public function getEventName(): string
    {
        return 'cinema.salle.created';
    }

    public function getAggregateId(): string
    {
        return $this->salleUuid;
    }

    public function getAggregateType(): string
    {
        return 'salle';
    }

    public function getSalleUuid(): string
    {
        return $this->salleUuid;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'salle_uuid' => $this->salleUuid,
            'event_type' => 'created',
        ];
    }
}
