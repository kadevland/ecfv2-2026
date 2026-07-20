<?php

declare(strict_types=1);

namespace App\Application\Film\DTOs;

final readonly class FilmDetailDto
{
    public function __construct(
        public string $uuid,
        public string $titre,
        public ?string $titreFr,
        /** @var array<string> */ public array $realisateurs,
        public ?string $realisateurPrincipal,
        /** @var array<string> */ public array $acteursPrincipaux,
        /** @var array<string> */ public array $genres,
        public int $dureeMinutes,
        public string $dureeFormatted,
        public string $classification,
        public string $langueOriginale,
        /** @var array<string> */ public array $sousTitres,
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
        public bool $isInTheaters,
        /** @var array<mixed> */ public array $seancesAVenir = [],
        /** @var array<mixed> */ public array $avisRecents = [],
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
            'realisateur_principal' => $this->realisateurPrincipal,
            'acteurs_principaux'    => $this->acteursPrincipaux,
            'genres'                => $this->genres,
            'duree_minutes'         => $this->dureeMinutes,
            'duree_formatted'       => $this->dureeFormatted,
            'classification'        => $this->classification,
            'langue_originale'      => $this->langueOriginale,
            'sous_titres'           => $this->sousTitres,
            'resume'                => $this->resume,
            'date_sortie'           => $this->dateSortie,
            'date_fin_exploitation' => $this->dateFinExploitation,
            'note_presse'           => $this->notePresse,
            'note_public'           => $this->notePublic,
            'note_moyenne_avis'     => $this->noteMoyenneAvis,
            'nombre_avis'           => $this->nombreAvis,
            'affiche_url'           => $this->afficheUrl,
            'bande_annonce_url'     => $this->bandeAnnonceUrl,
            'est_actif'             => $this->estActif,
            'is_in_theaters'        => $this->isInTheaters,
            'seances_a_venir'       => $this->seancesAVenir,
            'avis_recents'          => $this->avisRecents,
        ];
    }
}
