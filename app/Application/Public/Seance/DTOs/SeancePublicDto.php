<?php

declare(strict_types=1);

namespace App\Application\Public\Seance\DTOs;

final readonly class SeancePublicDto
{
    /**
     * @param string[] $technologies
     * @param array<string, mixed> $tarification
     */
    public function __construct(
        public string $seanceId,
        public string $filmId,
        public string $salleId,
        public string $cinemaId,
        public string $filmTitre,
        public string $salleNom,
        public string $cinemaNom,
        public string $dateHeureDebut,
        public string $dateHeureFin,
        public string $version,
        /** @var string[] */ public array $technologies,
        /** @var array<string, mixed> */ public array $tarification,
        public string $statut,
        public int $placesTotales,
        public int $placesDisponibles,
        public bool $estComplete,
        public bool $placementLibre,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'seance_id'          => $this->seanceId,
            'film_id'            => $this->filmId,
            'salle_id'           => $this->salleId,
            'cinema_id'          => $this->cinemaId,
            'film_titre'         => $this->filmTitre,
            'salle_nom'          => $this->salleNom,
            'cinema_nom'         => $this->cinemaNom,
            'date_heure_debut'   => $this->dateHeureDebut,
            'date_heure_fin'     => $this->dateHeureFin,
            'version'            => $this->version,
            'technologies'       => $this->technologies,
            'tarification'       => $this->tarification,
            'statut'             => $this->statut,
            'places_totales'     => $this->placesTotales,
            'places_disponibles' => $this->placesDisponibles,
            'est_complete'       => $this->estComplete,
            'placement_libre'    => $this->placementLibre,
        ];
    }

    public function getPrixMinimum(): float
    {
        if (empty($this->tarification)) {
            return 0.0;
        }

        $tarifs = [];
        if (isset($this->tarification['tarifsBase']) && is_array($this->tarification['tarifsBase'])) {
            $tarifs = array_values($this->tarification['tarifsBase']);
        }

        $tarifs = array_filter($tarifs, 'is_numeric');

        return !empty($tarifs) ? min($tarifs) / 100 : 0.0; // Conversion centimes vers euros
    }

    public function getPrixMaximum(): float
    {
        if (empty($this->tarification)) {
            return 0.0;
        }

        $tarifs = [];
        if (isset($this->tarification['tarifsBase']) && is_array($this->tarification['tarifsBase'])) {
            $tarifs = array_values($this->tarification['tarifsBase']);
        }

        $tarifs = array_filter($tarifs, 'is_numeric');

        return !empty($tarifs) ? max($tarifs) / 100 : 0.0; // Conversion centimes vers euros
    }

    public function isAvailable(): bool
    {
        return $this->statut === 'PROGRAMMEE' && $this->placesDisponibles > 0;
    }

    public function isFuture(): bool
    {
        return \Carbon\Carbon::parse($this->dateHeureDebut)->isFuture();
    }
}
