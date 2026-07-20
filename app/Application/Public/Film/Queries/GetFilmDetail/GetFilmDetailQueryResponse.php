<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmDetail;

final readonly class GetFilmDetailQueryResponse
{
    /**
     * @param array<string, mixed> $film
     * @param array<string, array<string, mixed>> $seances
     * @param array<int, array<string, string>> $cinemas
     */
    public function __construct(
        public array $film,
        public array $seances = [],
        public array $cinemas = []
    ) {}
}
