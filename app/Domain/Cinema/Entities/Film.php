<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Entities;

use DateTime;
use ValueError;
use DateTimeInterface;
use App\Domain\Cinema\Events\FilmCreated;
use App\Domain\Cinema\Events\FilmUpdated;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\Entities\AggregateRoot;

/**
 * @property FilmId $id
 * @property string $titre
 * @property string|null $titreOriginal
 * @property array<string> $realisateurs
 * @property array<string> $acteursPrincipaux
 * @property array<string> $genres
 * @property int $dureeMinutes
 * @property string $classification
 * @property string|null $langueOriginale
 * @property string|null $paysOrigine
 * @property string|null $producteur
 * @property array<string>|null $sousTitres
 * @property string|null $synopsis
 * @property DateTimeInterface $dateSortie
 * @property DateTimeInterface|null $dateFinExploitation
 * @property float|null $noteCritique
 * @property float|null $notePublic
 * @property float|null $noteMoyenneAvis
 * @property int $nombreAvis
 * @property string|null $afficheUrl
 * @property string|null $bandeAnnonceUrl
 * @property bool $estActif
 */
final class Film extends AggregateRoot
{
    public readonly FilmId $id;

    public private(set) string $titre;

    public private(set) ?string $titreOriginal;

    /** @var array<string> */
    public private(set) array $realisateurs;

    /** @var array<string> */
    public private(set) array $acteursPrincipaux;

    /** @var array<string> */
    public private(set) array $genres;

    public private(set) int $dureeMinutes;

    public private(set) string $classification;

    public private(set) ?string $langueOriginale;

    public private(set) ?string $paysOrigine;

    public private(set) ?string $producteur;

    /** @var array<string>|null */
    public private(set) ?array $sousTitres;

    public private(set) ?string $synopsis;

    public private(set) DateTimeInterface $dateSortie;

    public private(set) ?DateTimeInterface $dateFinExploitation;

    public private(set) ?float $noteCritique;

    public private(set) ?float $notePublic;

    public private(set) ?float $noteMoyenneAvis;

    public private(set) int $nombreAvis;

    public private(set) ?string $afficheUrl;

    public private(set) ?string $bandeAnnonceUrl;

    public private(set) bool $estActif;

    public private(set) ?string $statut;

    /**
     * @param array<string> $realisateurs
     * @param array<string> $genres
     * @param array<string>|null $acteursPrincipaux
     * @param array<string>|null $sousTitres
     */
    public function __construct(
        FilmId $id,
        string $titre,
        array $realisateurs,
        array $genres,
        int $dureeMinutes,
        string $classification,
        DateTimeInterface $dateSortie,
        ?string $titreOriginal = null,
        ?array $acteursPrincipaux = null,
        ?string $langueOriginale = null,
        ?string $paysOrigine = null,
        ?string $producteur = null,
        ?array $sousTitres = null,
        ?string $synopsis = null,
        ?DateTimeInterface $dateFinExploitation = null,
        ?float $noteCritique = null,
        ?float $notePublic = null,
        ?string $afficheUrl = null,
        ?string $bandeAnnonceUrl = null,
        bool $estActif = true,
        ?string $statut = null,
    ) {
        $this->id                  = $id;
        $this->titre               = $titre;
        $this->titreOriginal       = $titreOriginal;
        $this->realisateurs        = $realisateurs;
        $this->acteursPrincipaux   = $acteursPrincipaux ?? [];
        $this->genres              = $genres;
        $this->dureeMinutes        = $dureeMinutes;
        $this->classification      = $classification;
        $this->langueOriginale     = $langueOriginale;
        $this->paysOrigine         = $paysOrigine;
        $this->producteur          = $producteur;
        $this->sousTitres          = $sousTitres;
        $this->synopsis            = $synopsis;
        $this->dateSortie          = $dateSortie;
        $this->dateFinExploitation = $dateFinExploitation;
        $this->noteCritique        = $noteCritique;
        $this->notePublic          = $notePublic;
        $this->noteMoyenneAvis     = null;
        $this->nombreAvis          = 0;
        $this->afficheUrl          = $afficheUrl;
        $this->bandeAnnonceUrl     = $bandeAnnonceUrl;
        $this->estActif            = $estActif;
        $this->statut              = $statut;
    }

    /**
     * @param array<string> $realisateurs
     * @param array<string> $genres
     */
    public static function creer(
        string $titre,
        array $realisateurs,
        array $genres,
        int $dureeMinutes,
        string $classification,
        DateTimeInterface $dateSortie,
        ?string $titreOriginal = null,
        ?string $synopsis = null,
        ?string $paysOrigine = null,
    ): self {
        $film = new self(
            FilmId::generate(),
            $titre,
            $realisateurs,
            $genres,
            $dureeMinutes,
            $classification,
            $dateSortie,
            $titreOriginal,
            null, // acteurs principaux
            null, // langue originale
            $paysOrigine,
            null, // producteur
            null, // sous-titres
            $synopsis,
        );

        $film->addDomainEvent(FilmCreated::fromFilm($film));

        return $film;
    }

    /**
     * @param array<string> $realisateurs
     * @param array<string> $genres
     * @param array<string> $acteursPrincipaux
     */
    public static function create(
        string $titre,
        array $realisateurs,
        array $genres,
        int $dureeMinutes,
        string $classification,
        DateTimeInterface $dateSortie,
        ?string $titreFr = null,
        array $acteursPrincipaux = [],
        ?string $langueOriginale = null,
        ?string $sousTitres = null,
        ?string $resume = null,
        ?DateTimeInterface $dateFinExploitation = null,
        ?float $notePresse = null,
        ?float $notePublic = null,
        ?string $afficheUrl = null,
        ?string $bandeAnnonceUrl = null,
        bool $estActif = true,
    ): self {
        return new self(
            FilmId::generate(),
            $titre,
            $realisateurs,
            $genres,
            $dureeMinutes,
            $classification,
            $dateSortie,
            $titreFr,
            $acteursPrincipaux,
            $langueOriginale,
            null, // paysOrigine
            null, // producteur
            $sousTitres ? [$sousTitres] : null,
            $resume,
            $dateFinExploitation,
            $notePresse,
            $notePublic,
            $afficheUrl,
            $bandeAnnonceUrl,
            $estActif,
        );
    }

    public function isInTheaters(): bool
    {
        $now = new DateTime;

        return $this->estActif
            && $this->dateSortie <= $now
            && ($this->dateFinExploitation === null || $this->dateFinExploitation >= $now);
    }

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

    public function getClassificationLabel(): ?string
    {
        if (!$this->classification) {
            return null;
        }

        try {
            return \App\Domain\Enums\ClassificationFilm::from($this->classification)->label();
        } catch (ValueError $e) {
            return $this->classification; // Fallback vers la valeur brute si enum invalide
        }
    }

    public function ajouterAvis(float $note): void
    {
        $totalRatings   = $this->nombreAvis;
        $currentAverage = $this->noteMoyenneAvis ?? 0;

        $newTotal   = ($currentAverage * $totalRatings) + $note;
        $newCount   = $totalRatings + 1;
        $newAverage = $newTotal / $newCount;

        $this->noteMoyenneAvis = round($newAverage, 1);
        $this->nombreAvis      = $newCount;
    }

    public function changerTitre(string $nouveauTitre, ?string $nouveauTitreOriginal = null): void
    {
        $this->titre         = $nouveauTitre;
        $this->titreOriginal = $nouveauTitreOriginal;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    /**
     * @param array<string> $nouveauxRealisateurs
     */
    public function changerRealisateurs(array $nouveauxRealisateurs): void
    {
        $this->realisateurs = $nouveauxRealisateurs;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

     public function changerActeursPrincipaux(array $nouveauxActeursPrincipaux): void
    {
        $this->acteursPrincipaux = $nouveauxActeursPrincipaux;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    /**
     * @param array<string> $nouveauxGenres
     */
    public function changerGenres(array $nouveauxGenres): void
    {
        $this->genres = $nouveauxGenres;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerDuree(int $nouvelleDuree): void
    {
        $this->dureeMinutes = $nouvelleDuree;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerClassification(string $nouvelleClassification): void
    {
        $this->classification = $nouvelleClassification;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerDateSortie(DateTimeInterface $nouvelleDateSortie): void
    {
        $this->dateSortie = $nouvelleDateSortie;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerSynopsis(?string $nouveauSynopsis): void
    {
        $this->synopsis = $nouveauSynopsis;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerAffiche(?string $nouvelleAfficheUrl): void
    {
        $this->afficheUrl = $nouvelleAfficheUrl;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function changerBandeAnnonce(?string $nouvelleBandeAnnonceUrl): void
    {
        $this->bandeAnnonceUrl = $nouvelleBandeAnnonceUrl;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function terminerExploitation(?DateTimeInterface $dateFin = null): void
    {
        $this->dateFinExploitation = $dateFin ?? new DateTime;
        $this->estActif            = false;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function activer(): void
    {
        $this->estActif = true;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function desactiver(): void
    {
        $this->estActif = false;
        $this->addDomainEvent(FilmUpdated::fromFilm($this));
    }

    public function getPrimaryDirector(): ?string
    {
        return $this->realisateurs[0] ?? null;
    }
}
