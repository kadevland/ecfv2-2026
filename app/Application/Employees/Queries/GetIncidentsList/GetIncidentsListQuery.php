<?php

declare(strict_types=1);

namespace App\Application\Employees\Queries\GetIncidentsList;

use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\SeveriteIncident;
use App\Application\Contracts\QueryInterface;

final readonly class GetIncidentsListQuery implements QueryInterface
{
    public function __construct(
        public ?string $cinemaUuid = null,
        public ?string $emploiUuid = null,
        public ?string $salleUuid = null,
        public ?StatutIncident $statut = null,
        public ?SeveriteIncident $severite = null,
        public ?TypeIncident $type = null,
        public ?bool $openOnly = null,
        public ?bool $criticalOnly = null,
        public ?int $recentDays = null,
        public ?int $limit = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
    ) {}
}
