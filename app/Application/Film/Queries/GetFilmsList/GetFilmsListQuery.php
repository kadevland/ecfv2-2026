<?php

declare(strict_types=1);

namespace App\Application\Film\Queries\GetFilmsList;

use App\Application\Contracts\QueryInterface;

final readonly class GetFilmsListQuery implements QueryInterface
{
    /**
     * @param string[]|null $genres
     * @param array<string, mixed>|null $filters
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 20,
        public ?string $search = null,
        /** @var string[]|null */ public ?array $genres = null,
        public ?string $classification = null,
        public ?bool $enSalles = null,
        public ?bool $prochainement = null,
        /** @var array<string, mixed>|null */ public ?array $filters = null,
        // public ?string $sortBy = 'date_sortie',
        // public ?string $sortDirection = 'desc',
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1
            && $this->perPage >= 1
            && $this->perPage <= 100;
        // && in_array($this->sortBy, ['titre', 'date_sortie', 'duree', 'note_moyenne', 'created_at'])
        // && in_array($this->sortDirection, ['asc', 'desc']);
    }
}
