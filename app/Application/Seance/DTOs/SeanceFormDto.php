<?php

declare(strict_types=1);

namespace App\Application\Seance\DTOs;

final readonly class SeanceFormDto
{
    /**
     * @param array<string, float> $tarifsBase
     * @param array<string, mixed> $supplementsSpeciaux
     * @param array<string, mixed> $reductionsSpeciales
     */
    public function __construct(
        public ?string $uuid = null,
        public string $filmUuid = '',
        public string $salleUuid = '',
        public string $dateHeureDebut = '',
        public string $version = 'VF',
        public ?int $dureeAdditionnelle = 30,
        public ?string $qualiteProjection = null,
        public ?string $qualiteSonore = null,
        public bool $placementLibre = false,
        public string $statut = 'PROGRAMMEE',
        public array $tarifsBase = [],
        public array $supplementsSpeciaux = [],
        public array $reductionsSpeciales = [],
        public float $tauxTva = 20.0,
        public string $devise = 'EUR',
    ) {}

    /**
     * Créer un DTO depuis un SeanceDetailDto pour l'édition
     */
    public static function fromDetailDto(SeanceDetailDto $detail): self
    {

        return new self(
            uuid: $detail->uuid,
            filmUuid: $detail->filmUuid,
            salleUuid: $detail->salleUuid,
            dateHeureDebut: $detail->dateHeureDebut,
            version: $detail->version,
            dureeAdditionnelle: $detail->dureeAdditionnelle,
            qualiteProjection: $detail->qualiteProjection,
            qualiteSonore: $detail->qualiteSonore,
            placementLibre: $detail->placementLibre,
            statut: $detail->statut,
            tarifsBase: $detail->tarification['tarifsBase'] ?? [],
            supplementsSpeciaux: $detail->tarification['supplementsSpeciaux'] ?? [],
            reductionsSpeciales: $detail->tarification['reductionsSpeciales'] ?? [],
            tauxTva: $detail->tauxTva,
            devise: $detail->devise,
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
            'uuid'                 => $this->uuid,
            'film_uuid'            => $this->filmUuid,
            'salle_uuid'           => $this->salleUuid,
            'date_heure_debut'     => $this->dateHeureDebut,
            'version'              => $this->version,
            'duree_additionnelle'  => $this->dureeAdditionnelle,
            'qualite_projection'   => $this->qualiteProjection,
            'qualite_sonore'       => $this->qualiteSonore,
            'placement_libre'      => $this->placementLibre,
            'statut'               => $this->statut,
            'tarifs_base'          => $this->tarifsBase,
            'supplements_speciaux' => $this->supplementsSpeciaux,
            'reductions_speciales' => $this->reductionsSpeciales,
            'taux_tva'             => $this->tauxTva,
            'devise'               => $this->devise,
        ];
    }
}
