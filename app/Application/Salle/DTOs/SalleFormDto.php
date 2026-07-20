<?php

declare(strict_types=1);

namespace App\Application\Salle\DTOs;

final readonly class SalleFormDto
{
    /**
     * @param array<string> $qualiteProjection
     * @param array<string> $qualiteSonore
     * @param array<string, mixed>|null $planSalle
     */
    public function __construct(
        public ?string $uuid = null,
        public string $nom = '',
        public int $capaciteTotale = 0,
        public int $nombreRangees = 0,
        public int $placesParRangee = 0,
        public int $placesStandard = 0,
        public int $placesPmr = 0,
        public array $qualiteProjection = [],
        public array $qualiteSonore = [],
        public bool $climatisation = true,
        public bool $accessibilitePmr = true,
        public ?array $planSalle = null,
        public string $statut = 'ACTIVE',
        public ?string $cinemaUuid = null,
        public ?string $cinemaNom = null,
        public ?string $cinemaVille = null,
    ) {}

    /**
     * Créer un DTO depuis un SalleDetailDto pour l'édition
     */
    public static function fromDetailDto(SalleDetailDto $detail): self
    {
        return new self(
            uuid: $detail->uuid,
            nom: $detail->nom,
            capaciteTotale: $detail->capaciteTotale,
            nombreRangees: $detail->nombreRangees,
            placesParRangee: $detail->placesParRangee,
            placesStandard: $detail->placesStandard,
            placesPmr: $detail->placesPmr,
            qualiteProjection: $detail->qualiteProjection,
            qualiteSonore: $detail->qualiteSonore,
            climatisation: $detail->climatisation,
            accessibilitePmr: $detail->accessibilitePmr,
            planSalle: $detail->planSalle,
            statut: $detail->statut,
            cinemaUuid: $detail->cinemaUuid,
            cinemaNom: $detail->cinemaNom,
            cinemaVille: $detail->cinemaVille,
        );
    }

    /**
     * Créer un DTO vide pour la création
     */
    public static function empty(): self
    {
        return new self;
    }

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
        ];
    }
}
