<?php

declare(strict_types=1);

namespace App\Application\Seance\DTOs;

final readonly class SeanceDetailDto
{
    /**
     * @param array<string> $technologies
     * @param array<string, mixed> $tarification
     * @param array<mixed> $reservations
     * @param array<string> $placesOccupees
     */
    public function __construct(
        public string $uuid,
        public string $dateHeure,
        public string $dateHeureDebut,
        public string $dateHeureFin,
        public string $filmTitre,
        public string $filmUuid,
        public string $filmAfficheUrl,
        public int $filmDureeMinutes,
        public ?string $filmClassification,
        public ?string $filmClassificationLabel,
        public string $salleNom,
        public string $salleUuid,
        public int $salleNumero,
        public int $salleCapacite,
        public string $version,
        public array $technologies,
        public array $tarification,
        public ?int $dureeAdditionnelle,
        public ?string $qualiteProjection,
        public ?string $qualiteSonore,
        public bool $placementLibre,
        public int $placesDisponibles,
        public int $placesTotales,
        public string $statut,
        public string $statutLabel,
        public float $tauxTva,
        public string $devise,
        public bool $estComplete = false,
        public bool $estAnnulee = false,
        public array $reservations = [],
        public array $placesOccupees = [],
    ) {}

    /**
     * Retourne la liste des statuts disponibles pour les formulaires
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getStatutsDisponibles(): array
    {
        return array_map(function ($statut) {
            return [
                'value'       => $statut->value,
                'label'       => $statut->label(),
                'badge_class' => $statut->getBadgeClass(),
                'color_class' => $statut->getColorClass(),
            ];
        }, \App\Domain\Enums\StatutSeance::cases());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'                      => $this->uuid,
            'date_heure'                => $this->dateHeure,
            'date_heure_debut'          => $this->dateHeureDebut,
            'date_heure_fin'            => $this->dateHeureFin,
            'film_titre'                => $this->filmTitre,
            'film_uuid'                 => $this->filmUuid,
            'film_affiche_url'          => $this->filmAfficheUrl,
            'film_duree_minutes'        => $this->filmDureeMinutes,
            'film_classification'       => $this->filmClassification,
            'film_classification_label' => $this->filmClassificationLabel,
            'salle_nom'                 => $this->salleNom,
            'salle_uuid'                => $this->salleUuid,
            'salle_numero'              => $this->salleNumero,
            'salle_capacite'            => $this->salleCapacite,
            'version'                   => $this->version,
            'technologies'              => $this->technologies,
            'tarification'              => $this->tarification,
            'duree_additionnelle'       => $this->dureeAdditionnelle,
            'qualite_projection'        => $this->qualiteProjection,
            'qualite_sonore'            => $this->qualiteSonore,
            'placement_libre'           => $this->placementLibre,
            'places_disponibles'        => $this->placesDisponibles,
            'places_totales'            => $this->placesTotales,
            'statut'                    => $this->statut,
            'statut_label'              => $this->statutLabel,
            'est_complete'              => $this->estComplete,
            'est_annulee'               => $this->estAnnulee,
            'reservations'              => $this->reservations,
            'places_occupees'           => $this->placesOccupees,
        ];
    }
}
