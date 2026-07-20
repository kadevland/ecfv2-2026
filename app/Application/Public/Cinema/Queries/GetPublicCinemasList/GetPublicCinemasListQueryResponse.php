<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemasList;

use App\Application\Cinema\DTOs\CinemaListItemDto;

/**
 * Réponse pour la liste publique des cinémas
 */
final readonly class GetPublicCinemasListQueryResponse
{
    /**
     * @param CinemaListItemDto[] $cinemas
     */
    public function __construct(
        public array $cinemas,
        public int $total,
        public int $page,
        public int $perPage
    ) {}
}
