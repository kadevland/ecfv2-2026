<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface de base pour toutes les Queries CQRS
 *
 * Les Queries représentent des demandes de lecture de données.
 * Elles ne modifient jamais l'état du système (side-effect free).
 */
interface QueryInterface
{
    // Marker interface - pas de méthodes requises
    // Les Queries sont des DTOs immutables
}
