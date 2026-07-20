<?php

declare(strict_types=1);

namespace App\Application\Seance\Queries\GetSeanceDetail;

use App\Application\Contracts\QueryInterface;

final readonly class GetSeanceDetailQuery implements QueryInterface
{
    public function __construct(
        public string $seanceUuid,
        public bool $includeReservations = false,
        public bool $includePlacesOccupees = false,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->seanceUuid);
    }
}
