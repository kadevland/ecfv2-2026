<?php

declare(strict_types=1);

namespace App\Application\Salle\DTOs;

final readonly class SalleListItemDto
{
    /**
     * @param array<string> $qualiteProjection
     * @param array<string> $qualiteSonore
     * @param array<string, mixed>|null $planSalle
     */
    public function __construct(
        public string $uuid,
        public string $nom,
        public int $capaciteTotale,
        public int $nombreRangees,
        public int $placesParRangee,
        public int $placesStandard,
        public int $placesPmr,
        public array $qualiteProjection,
        public array $qualiteSonore,
        public bool $accessibilitePmr,
        public bool $climatisation,
        public ?array $planSalle,
        public string $statut,
        public bool $estDisponible = true,
        public ?string $cinemaNom = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'               => $this->uuid,
            'nom'                => $this->nom,
            'capacite_totale'    => $this->capaciteTotale,
            'nombre_rangees'     => $this->nombreRangees,
            'places_par_rangee'  => $this->placesParRangee,
            'places_standard'    => $this->placesStandard,
            'places_pmr'         => $this->placesPmr,
            'qualite_projection' => $this->qualiteProjection,
            'qualite_sonore'     => $this->qualiteSonore,
            'accessibilite_pmr'  => $this->accessibilitePmr,
            'climatisation'      => $this->climatisation,
            'plan_salle'         => $this->planSalle,
            'statut'             => $this->statut,
            'est_disponible'     => $this->estDisponible,
            'cinema_nom'         => $this->cinemaNom,
        ];
    }
}
