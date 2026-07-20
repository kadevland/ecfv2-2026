<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemaDetail;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer le détail d'un cinéma public par UUID
 */
final readonly class GetPublicCinemaDetailQuery implements QueryInterface
{
    public function __construct(
        public string $cinemaUuid
    ) {}
}
