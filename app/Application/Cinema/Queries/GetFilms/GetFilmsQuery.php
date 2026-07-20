<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetFilms;

use App\Application\Contracts\QueryInterface;

final readonly class GetFilmsQuery implements QueryInterface
{
    public function __construct(
        public ?string $genre = null,
        public ?string $statut = null,
        public ?bool $estActif = null,
        public int $page = 1,
        public int $perPage = 20,
        public string $sortBy = 'titre',
        public string $sortDirection = 'asc',
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1 &&
               $this->perPage >= 1 &&
               $this->perPage <= 100 &&
               in_array($this->sortDirection, ['asc', 'desc']);
    }
}
