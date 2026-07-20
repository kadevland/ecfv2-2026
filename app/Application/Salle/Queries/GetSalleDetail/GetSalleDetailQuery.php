<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSalleDetail;

use App\Application\Contracts\QueryInterface;

final readonly class GetSalleDetailQuery implements QueryInterface
{
    public function __construct(
        public string $salleUuid,
        public bool $includeSeances = false,
        public bool $includeMaintenances = false,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->salleUuid);
    }
}
