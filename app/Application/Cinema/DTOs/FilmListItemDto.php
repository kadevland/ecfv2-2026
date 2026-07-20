<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

final readonly class FilmListItemDto
{
    /**
     * @param string[] $realisateurs
     * @param string[] $acteursPrincipaux
     * @param string[] $genres
     * @param string[]|null $sousTitres
     */
    public function __construct(
        public string $uuid,
        public string $titre,
        public ?string $titreFr,
        public array $realisateurs,
        public array $acteursPrincipaux,
        public array $genres,
        public int $dureeMinutes,
        public string $classification,
        public ?string $langueOriginale,
        public ?array $sousTitres,
        public ?string $resume,
        public string $dateSortie,
        public ?string $dateFinExploitation,
        public ?float $notePresse,
        public ?float $notePublic,
        public ?float $noteMoyenneAvis,
        public int $nombreAvis,
        public ?string $afficheUrl,
        public ?string $bandeAnnonceUrl,
        public bool $estActif,
    ) {}

    public function getFormattedDuration(): string
    {
        $hours   = intval($this->dureeMinutes / 60);
        $minutes = $this->dureeMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    public function getGenresString(): string
    {
        return implode(', ', $this->genres);
    }

    public function getRealisateursString(): string
    {
        return implode(', ', $this->realisateurs);
    }

    public function getPrimaryDirector(): ?string
    {
        return $this->realisateurs[0] ?? null;
    }

    public function getFormattedReleaseDate(): string
    {
        return date('d/m/Y', strtotime($this->dateSortie));
    }

    public function isInTheaters(): bool
    {
        $now              = time();
        $releaseTimestamp = strtotime($this->dateSortie);
        $endTimestamp     = $this->dateFinExploitation ? strtotime($this->dateFinExploitation) : null;

        return $this->estActif
            && $releaseTimestamp <= $now
            && ($endTimestamp === null || $endTimestamp >= $now);
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
            'uuid'                   => $this->uuid,
            'titre'                  => $this->titre,
            'titre_fr'               => $this->titreFr,
            'realisateurs'           => $this->realisateurs,
            'acteurs_principaux'     => $this->acteursPrincipaux,
            'genres'                 => $this->genres,
            'duree_minutes'          => $this->dureeMinutes,
            'classification'         => $this->classification,
            'langue_originale'       => $this->langueOriginale,
            'sous_titres'            => $this->sousTitres,
            'resume'                 => $this->resume,
            'date_sortie'            => $this->dateSortie,
            'date_fin_exploitation'  => $this->dateFinExploitation,
            'note_presse'            => $this->notePresse,
            'note_public'            => $this->notePublic,
            'note_moyenne_avis'      => $this->noteMoyenneAvis,
            'nombre_avis'            => $this->nombreAvis,
            'affiche_url'            => $this->afficheUrl,
            'bande_annonce_url'      => $this->bandeAnnonceUrl,
            'est_actif'              => $this->estActif,
            'formatted_duration'     => $this->getFormattedDuration(),
            'genres_string'          => $this->getGenresString(),
            'realisateurs_string'    => $this->getRealisateursString(),
            'primary_director'       => $this->getPrimaryDirector(),
            'formatted_release_date' => $this->getFormattedReleaseDate(),
            'is_in_theaters'         => $this->isInTheaters(),
        ];
    }
}
