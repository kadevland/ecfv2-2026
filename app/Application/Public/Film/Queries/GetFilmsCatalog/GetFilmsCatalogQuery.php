<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmsCatalog;

use App\Application\Contracts\QueryInterface;

final readonly class GetFilmsCatalogQuery implements QueryInterface
{
    public function __construct(
        public ?string $genre = null,
        public ?string $classification = null,
        public ?string $search = null,
        public bool $inTheaters = false,
        public ?int $page = 1,
        public ?int $perPage = 20,
        public string $sortBy = 'date_sortie',
        public string $sortDirection = 'desc',
    ) {}
}
