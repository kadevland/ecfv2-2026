<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemaDetail;

use App\Application\Cinema\DTOs\CinemaDetailDto;

/**
 * Réponse pour le détail d'un cinéma public
 */
final readonly class GetPublicCinemaDetailQueryResponse
{
    public function __construct(
        public CinemaDetailDto $cinema
    ) {}
}
