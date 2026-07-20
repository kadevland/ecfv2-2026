<?php

declare(strict_types=1);

namespace App\Application\Seance\DTOs;

final readonly class SeanceListItemDto
{
    /**
     * @param array<string> $technologies
     */
    public function __construct(
        public string $uuid,
        public string $dateHeure,
        public string $dateHeureDebut,
        public string $dateHeureFin,
        public string $filmTitre,
        public string $filmUuid,
        public string $salleNom,
        public string $salleUuid,
        public int $salleNumero,
        public string $cinemaNom,
        public string $cinemaUuid,
        public string $salleDisplayName, // "Cinéma X - Salle Y"
        public string $version,
        public array $technologies,
        public float $prixMin,
        public float $prixMax,
        public int $placesDisponibles,
        public int $placesTotales,
        public string $statut,
        public bool $estComplete = false,
        public bool $estAnnulee = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'               => $this->uuid,
            'date_heure'         => $this->dateHeure,
            'date_heure_debut'   => $this->dateHeureDebut,
            'date_heure_fin'     => $this->dateHeureFin,
            'film_titre'         => $this->filmTitre,
            'film_uuid'          => $this->filmUuid,
            'salle_nom'          => $this->salleNom,
            'salle_uuid'         => $this->salleUuid,
            'salle_numero'       => $this->salleNumero,
            'cinema_nom'         => $this->cinemaNom,
            'cinema_uuid'        => $this->cinemaUuid,
            'salle_display_name' => $this->salleDisplayName,
            'version'            => $this->version,
            'technologies'       => $this->technologies,
            'prix_min'           => $this->prixMin,
            'prix_max'           => $this->prixMax,
            'places_disponibles' => $this->placesDisponibles,
            'places_totales'     => $this->placesTotales,
            'statut'             => $this->statut,
            'est_complete'       => $this->estComplete,
            'est_annulee'        => $this->estAnnulee,
        ];
    }
}
