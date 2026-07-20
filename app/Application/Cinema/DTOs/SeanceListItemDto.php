<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

final readonly class SeanceListItemDto
{
    /**
     * @param array<string, mixed> $tarification
     * @param array<string, mixed>|null $optionsSupplementaires
     */
    public function __construct(
        public string $uuid,
        public string $filmUuid,
        public string $filmTitre,
        public string $salleUuid,
        public string $salleNom,
        public string $cinemaUuid,
        public string $cinemaNom,
        public string $dateHeureDebut,
        public string $dateHeureFin,
        public string $version,
        public array $tarification,
        public bool $placementLibre,
        public string $statut,
        public ?array $optionsSupplementaires,
    ) {}

    public function getFormattedTime(): string
    {
        return date('H:i', strtotime($this->dateHeureDebut));
    }

    public function getFormattedDate(): string
    {
        return date('d/m/Y', strtotime($this->dateHeureDebut));
    }

    public function getFormattedDateTime(): string
    {
        return date('d/m/Y H:i', strtotime($this->dateHeureDebut));
    }

    public function getDuration(): string
    {
        $debut           = strtotime($this->dateHeureDebut);
        $fin             = strtotime($this->dateHeureFin);
        $durationMinutes = round(($fin - $debut) / 60);

        return $durationMinutes . ' min';
    }

    public function isPast(): bool
    {
        return strtotime($this->dateHeureDebut) < time();
    }

    public function isUpcoming(): bool
    {
        return strtotime($this->dateHeureDebut) > time();
    }

    public function isPlaying(): bool
    {
        $now = time();

        return strtotime($this->dateHeureDebut) <= $now && strtotime($this->dateHeureFin) > $now;
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'                    => $this->uuid,
            'film_uuid'               => $this->filmUuid,
            'film_titre'              => $this->filmTitre,
            'salle_uuid'              => $this->salleUuid,
            'salle_nom'               => $this->salleNom,
            'cinema_uuid'             => $this->cinemaUuid,
            'cinema_nom'              => $this->cinemaNom,
            'date_heure_debut'        => $this->dateHeureDebut,
            'date_heure_fin'          => $this->dateHeureFin,
            'version'                 => $this->version,
            'tarification'            => $this->tarification,
            'placement_libre'         => $this->placementLibre,
            'statut'                  => $this->statut,
            'options_supplementaires' => $this->optionsSupplementaires,
            'formatted_time'          => $this->getFormattedTime(),
            'formatted_date'          => $this->getFormattedDate(),
            'formatted_datetime'      => $this->getFormattedDateTime(),
            'duration'                => $this->getDuration(),
            'is_past'                 => $this->isPast(),
            'is_upcoming'             => $this->isUpcoming(),
            'is_playing'              => $this->isPlaying(),
        ];
    }
}
