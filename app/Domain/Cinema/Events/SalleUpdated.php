<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Salle;
use App\Domain\Shared\Events\DomainEvent;

final class SalleUpdated extends DomainEvent
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

    public static function fromUuid(string $salleUuid): self
    {
        return new self($salleUuid);
    }

    public function getEventName(): string
    {
        return 'cinema.salle.updated';
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
            'event_type' => 'updated',
        ];
    }
}
