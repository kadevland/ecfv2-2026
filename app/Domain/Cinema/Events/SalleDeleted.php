<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\SalleId;

/**
 * Événement déclenché lors de la suppression d'une salle
 */
final class SalleDeleted extends DomainEvent
{
    private function __construct(
        private readonly string $salleUuid
    ) {
        parent::__construct();
    }

    public static function fromSalleId(SalleId $salleId): self
    {
        return new self($salleId->value);
    }

    public function getEventName(): string
    {
        return 'cinema.salle.deleted';
    }

    public function getAggregateId(): string
    {
        return $this->salleUuid;
    }

    public function getSalleUuid(): string
    {
        return $this->salleUuid;
    }

    public function getAggregateType(): string
    {
        return 'salle';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'salle_uuid' => $this->salleUuid,
            'event_type' => 'deleted',
        ];
    }
}
