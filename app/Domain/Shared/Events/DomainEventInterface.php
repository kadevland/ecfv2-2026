<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

use DateTimeInterface;

/**
 * Interface de base pour tous les événements du domaine
 *
 * Les événements représentent quelque chose qui s'est passé dans le domaine
 * et qui peut intéresser d'autres parties du système.
 */
interface DomainEventInterface
{
    /**
     * Identifiant unique de l'événement
     */
    public function getEventId(): string;

    /**
     * Nom de l'événement (ex: "cinema.seance.created")
     */
    public function getEventName(): string;

    /**
     * Version de l'événement pour gérer l'évolution du schéma
     */
    public function getEventVersion(): string;

    /**
     * Timestamp de l'événement
     */
    public function getOccurredOn(): DateTimeInterface;

    /**
     * Données de l'événement sous forme de tableau
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Agrégat root ID concerné par l'événement
     */
    public function getAggregateId(): string;

    /**
     * Type d'agrégat concerné (ex: "seance", "film", "cinema")
     */
    public function getAggregateType(): string;
}
