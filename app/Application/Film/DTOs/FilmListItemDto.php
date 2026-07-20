<?php

declare(strict_types=1);

namespace App\Application\Film\DTOs;

final readonly class FilmListItemDto
{
    /**
     * @param string[] $realisateurs
     * @param string[] $genres
     */
    public function __construct(
        public string $uuid,
        public string $titre,
        public ?string $titreFr,
        public array $realisateurs,
        public array $genres,
        public int $dureeMinutes,
        public string $dureeFormatted,
        public string $classification,
        public string $dateSortie,
        public ?string $dateFinExploitation,
        public ?float $notePresse,
        public ?float $notePublic,
        public ?float $noteMoyenneAvis,
        public int $nombreAvis,
        public ?string $afficheUrl,
        public bool $estActif,
        public bool $isInTheaters,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'                  => $this->uuid,
            'titre'                 => $this->titre,
            'titre_fr'              => $this->titreFr,
            'realisateurs'          => $this->realisateurs,
            'genres'                => $this->genres,
            'duree_minutes'         => $this->dureeMinutes,
            'duree_formatted'       => $this->dureeFormatted,
            'classification'        => $this->classification,
            'date_sortie'           => $this->dateSortie,
            'date_fin_exploitation' => $this->dateFinExploitation,
            'note_presse'           => $this->notePresse,
            'note_public'           => $this->notePublic,
            'note_moyenne_avis'     => $this->noteMoyenneAvis,
            'nombre_avis'           => $this->nombreAvis,
            'affiche_url'           => $this->afficheUrl,
            'est_actif'             => $this->estActif,
            'is_in_theaters'        => $this->isInTheaters,
        ];
    }
}
