<?php

declare(strict_types=1);

namespace App\Application\Salle\DTOs;

final readonly class SalleEditDto
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
        public bool $climatisation,
        public bool $accessibilitePmr,
        public ?array $planSalle,
        public string $statut,
        public string $cinemaUuid,
        public string $cinemaNom,
        public string $cinemaVille,
        public bool $estDisponible,
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
            'climatisation'      => $this->climatisation,
            'accessibilite_pmr'  => $this->accessibilitePmr,
            'plan_salle'         => $this->planSalle,
            'statut'             => $this->statut,
            'cinema_uuid'        => $this->cinemaUuid,
            'cinema_nom'         => $this->cinemaNom,
            'cinema_ville'       => $this->cinemaVille,
            'est_disponible'     => $this->estDisponible,
        ];
    }
}
