<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemasList;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer la liste publique des cinémas depuis MongoDB
 */
final readonly class GetPublicCinemasListQuery implements QueryInterface
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 12,
        public ?string $location = null,
        /** @var array<string, mixed> */
        public array $filters = []
    ) {}
}
