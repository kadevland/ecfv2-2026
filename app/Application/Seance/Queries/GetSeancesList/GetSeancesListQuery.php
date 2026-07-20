<?php

declare(strict_types=1);

namespace App\Application\Seance\Queries\GetSeancesList;

use App\Application\Contracts\QueryInterface;

final readonly class GetSeancesListQuery implements QueryInterface
{
    /**
     * @param array<string>|null $technologies
     * @param array<string, mixed>|null $filters
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public ?string $search = null,
        public ?string $filmUuid = null,
        public ?string $salleUuid = null,
        public ?string $cinemaUuid = null,
        public ?string $dateDebut = null,
        public ?string $dateFin = null,
        public ?string $version = null,
        public ?array $technologies = null,
        public ?string $statut = null,
        public ?bool $seancesAVenir = null,
        public ?string $sortBy = 'date_heure_debut',
        public ?string $sortDirection = 'desc',
        public ?array $filters = null,
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1 &&
               $this->perPage >= 1 &&
               $this->perPage <= 100;
    }
}
