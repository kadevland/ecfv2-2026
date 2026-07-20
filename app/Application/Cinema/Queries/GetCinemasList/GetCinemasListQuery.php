<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemasList;

use App\Application\Contracts\QueryInterface;

final readonly class GetCinemasListQuery implements QueryInterface
{
    /**
     * @param array<string, mixed>|null $filters
     */
    public function __construct(
        public ?string $location = null,
        public ?array $filters = null,
        public int $page = 1,
        public int $perPage = 20,
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1 &&
               $this->perPage >= 1 &&
               $this->perPage <= 100;
    }
}
