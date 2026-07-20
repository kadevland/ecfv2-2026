<?php

declare(strict_types=1);

namespace App\Application\Shared\DTOs;

final readonly class PaginatedResultDto
{
    /**
     * @param array<mixed> $items Les éléments de la page courante
     * @param int $total Le nombre total d'éléments
     * @param int $page La page courante (1-indexed)
     * @param int $perPage Le nombre d'éléments par page
     */
    public function __construct(
        /** @var array<mixed> */ public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    /**
     * Calcule le nombre total de pages
     */
    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Indique s'il y a une page suivante
     */
    public function hasNextPage(): bool
    {
        return $this->page < $this->totalPages();
    }

    /**
     * Indique s'il y a une page précédente
     */
    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    /**
     * Retourne le numéro de la première page
     */
    public function firstPage(): int
    {
        return 1;
    }

    /**
     * Retourne le numéro de la dernière page
     */
    public function lastPage(): int
    {
        return $this->totalPages();
    }

    /**
     * Retourne le nombre d'éléments sur la page courante
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Indique si la collection est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Indique si la collection n'est pas vide
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Retourne la représentation en array (compatible avec les vues)
     *
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => $this->items,
            'meta' => [
                'total'             => $this->total,
                'page'              => $this->page,
                'per_page'          => $this->perPage,
                'total_pages'       => $this->totalPages(),
                'has_next_page'     => $this->hasNextPage(),
                'has_previous_page' => $this->hasPreviousPage(),
                'first_page'        => $this->firstPage(),
                'last_page'         => $this->lastPage(),
                'count'             => $this->count(),
            ],
        ];
    }
}
