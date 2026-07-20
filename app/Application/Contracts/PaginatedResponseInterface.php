<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface pour les réponses paginées des Use Cases
 *
 * Garantit que toute réponse paginée expose les données nécessaires
 * pour créer un LengthAwarePaginator côté Infrastructure
 */
interface PaginatedResponseInterface
{
    /**
     * Récupère les items de la page courante
     *
     * @return array<mixed>
     */
    public function getItems(): array;

    /**
     * Récupère le nombre total d'items
     */
    public function getTotal(): int;

    /**
     * Récupère la page courante
     */
    public function getPage(): int;

    /**
     * Récupère le nombre d'items par page
     */
    public function getPerPage(): int;
}
