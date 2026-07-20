<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmDetail;

use App\Application\Contracts\QueryInterface;

final readonly class GetFilmDetailQuery implements QueryInterface
{
    public function __construct(
        public string $filmId
    ) {}
}
