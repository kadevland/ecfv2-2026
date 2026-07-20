<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemaDetail;

use App\Application\Contracts\QueryInterface;

final readonly class GetCinemaDetailQuery implements QueryInterface
{
    public function __construct(
        public string $cinemaUuid,
        public bool $includeSalles = false,
        public bool $includeSeances = false,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->cinemaUuid);
    }
}
