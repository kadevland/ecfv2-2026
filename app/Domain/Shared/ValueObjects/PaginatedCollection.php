<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

final readonly class PaginatedCollection
{
    /**
     * @param array<mixed> $items Les entités de la page courante
     * @param int $total Le nombre total d'entités
     * @param PaginationCriteria $criteria Les critères de pagination utilisés
     */
    public function __construct(
        public array $items,
        public int $total,
        public PaginationCriteria $criteria,
    ) {}

    /**
     * Propriété publique pour accès direct au currentPage (compatibilité vue)
     */
    public function __get(string $name): mixed
    {
        if ($name === 'currentPage') {
            return $this->currentPage();
        }
        if ($name === 'total') {
            return $this->total;
        }

        return null;
    }

    /**
     * Calcule le nombre total de pages
     */
    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->criteria->perPage);
    }

    /**
     * Retourne la page courante
     */
    public function currentPage(): int
    {
        return $this->criteria->page;
    }

    /**
     * Retourne le nombre d'éléments par page
     */
    public function perPage(): int
    {
        return $this->criteria->perPage;
    }

    /**
     * Indique s'il y a une page suivante
     */
    public function hasNextPage(): bool
    {
        return $this->criteria->page < $this->totalPages();
    }

    /**
     * Indique s'il y a une page précédente
     */
    public function hasPreviousPage(): bool
    {
        return $this->criteria->page > 1;
    }

    /**
     * Nombre d'éléments sur la page courante
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
     * Indique s'il y a plusieurs pages (pour compatibilité Laravel pagination)
     */
    public function hasMultiplePages(): bool
    {
        return $this->totalPages() > 1;
    }

    /**
     * Retourne le numéro de la page précédente ou null s'il n'y en a pas
     */
    public function previousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->criteria->page - 1 : null;
    }

    /**
     * Retourne le numéro de la page suivante ou null s'il n'y en a pas
     */
    public function nextPage(): ?int
    {
        return $this->hasNextPage() ? $this->criteria->page + 1 : null;
    }

    /**
     * Retourne l'index du premier élément de la page (1-based)
     */
    public function from(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return ($this->criteria->page - 1) * $this->criteria->perPage + 1;
    }

    /**
     * Retourne l'index du dernier élément de la page (1-based)
     */
    public function to(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return min($this->from() + $this->count() - 1, $this->total);
    }

    /**
     * Retourne un tableau des numéros de pages à afficher
     *
     * @return array<int>
     */
    public function getPageRange(int $onEachSide = 3): array
    {
        $totalPages  = $this->totalPages();
        $currentPage = $this->criteria->page;

        if ($totalPages <= 10) {
            return range(1, $totalPages);
        }

        $start = max(1, $currentPage - $onEachSide);
        $end   = min($totalPages, $currentPage + $onEachSide);

        if ($currentPage <= $onEachSide + 1) {
            $end = min($totalPages, 7);
        }

        if ($currentPage >= $totalPages - $onEachSide) {
            $start = max(1, $totalPages - 6);
        }

        return range($start, $end);
    }
}
