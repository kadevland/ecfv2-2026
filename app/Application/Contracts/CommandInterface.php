<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface de base pour toutes les Commands CQRS
 *
 * Les Commands représentent des intentions de modification de l'état du système.
 * Elles encapsulent toutes les données nécessaires pour effectuer une action métier.
 */
interface CommandInterface
{
    // Marker interface - pas de méthodes requises
    // Les Commands sont des DTOs immutables
}
