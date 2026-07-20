<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Seance;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la création d'une séance
 *
 * Permet de notifier d'autres parties du système (ex: synchronisation MongoDB,
 * envoi de notifications, mise à jour des caches, etc.)
 */
final class SeanceCreated extends DomainEvent
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

    public function getEventName(): string
    {
        return 'cinema.seance.created';
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
     * Désactiver temporairement pour debug
     */
    public function isQueueEvent(): bool
    {
        return true; // Retour ASYNC
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'seance_uuid' => $this->seanceUuid,
            'event_type'  => 'created',
        ];
    }
}
