<?php

declare(strict_types=1);

namespace App\Application\Film\Queries\GetFilmDetail;

use App\Application\Contracts\QueryInterface;

final readonly class GetFilmDetailQuery implements QueryInterface
{
    public function __construct(
        public string $filmUuid,
        public bool $includeSeances = false,
        public bool $includeAvis = false,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->filmUuid);
    }
}
