<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSalleForEdit;

use App\Application\Contracts\QueryInterface;

final readonly class GetSalleForEditQuery implements QueryInterface
{
    public function __construct(
        public string $salleUuid,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->salleUuid);
    }
}
