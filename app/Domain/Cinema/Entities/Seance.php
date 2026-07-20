<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Entities;

use DateTime;
use DateTimeInterface;
use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\Events\SeanceCreated;
use App\Domain\Cinema\Events\SeanceUpdated;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Shared\Entities\AggregateRoot;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\ValueObjects\Tarification;

/**
 * @property SeanceId $id
 * @property FilmId $filmId
 * @property SalleId $salleId
 * @property DateTimeInterface $dateHeureDebut
 * @property DateTimeInterface $dateHeureFin
 * @property string $version
 * @property Tarification $tarification
 * @property TauxTva $tauxTva
 * @property Devise $devise
 * @property bool $placementLibre
 * @property StatutSeance $statut
 * @property int|null $dureeAdditionnelle
 * @property QualiteProjection|null $qualiteProjection
 * @property QualiteSonore|null $qualiteSonore
 * @property Film|null $film
 * @property Salle|null $salle
 */
final class Seance extends AggregateRoot
{
    public readonly SeanceId $id;

    public private(set) FilmId $filmId;

    public private(set) SalleId $salleId;

    public private(set) DateTimeInterface $dateHeureDebut;

    public private(set) DateTimeInterface $dateHeureFin;

    public private(set) string $version;

    public private(set) Tarification $tarification;

    public private(set) TauxTva $tauxTva;

    public private(set) Devise $devise;

    public private(set) bool $placementLibre;

    public private(set) StatutSeance $statut;

    public private(set) ?int $dureeAdditionnelle;

    public private(set) ?QualiteProjection $qualiteProjection;

    public private(set) ?QualiteSonore $qualiteSonore;

    public function __construct(
        SeanceId $id,
        FilmId $filmId,
        SalleId $salleId,
        DateTimeInterface $dateHeureDebut,
        DateTimeInterface $dateHeureFin,
        string $version,
        Tarification $tarification,
        TauxTva $tauxTva,
        Devise $devise,
        bool $placementLibre = false,
        StatutSeance $statut = StatutSeance::PROGRAMMEE,
        ?int $dureeAdditionnelle = null,
        ?QualiteProjection $qualiteProjection = null,
        ?QualiteSonore $qualiteSonore = null,
    ) {
        $this->id                 = $id;
        $this->filmId             = $filmId;
        $this->salleId            = $salleId;
        $this->dateHeureDebut     = $dateHeureDebut;
        $this->dateHeureFin       = $dateHeureFin;
        $this->version            = $version;
        $this->tarification       = $tarification;
        $this->tauxTva            = $tauxTva;
        $this->devise             = $devise;
        $this->placementLibre     = $placementLibre;
        $this->statut             = $statut;
        $this->dureeAdditionnelle = $dureeAdditionnelle;
        $this->qualiteProjection  = $qualiteProjection;
        $this->qualiteSonore      = $qualiteSonore;
    }

    public static function programmer(
        FilmId $filmId,
        SalleId $salleId,
        DateTimeInterface $dateHeureDebut,
        DateTimeInterface $dateHeureFin,
        string $version,
        Tarification $tarification,
        TauxTva $tauxTva,
        Devise $devise,
        bool $placementLibre = false,
    ): self {
        $seance = new self(
            SeanceId::generate(),
            $filmId,
            $salleId,
            $dateHeureDebut,
            $dateHeureFin,
            $version,
            $tarification,
            $tauxTva,
            $devise,
            $placementLibre,
        );

        $seance->addDomainEvent(SeanceCreated::fromSeance($seance));

        return $seance;
    }

    public function isPast(): bool
    {
        return $this->dateHeureDebut < new DateTime;
    }

    public function isPlaying(): bool
    {
        $now = new DateTime;

        return $this->dateHeureDebut <= $now && $this->dateHeureFin > $now;
    }

    public function isUpcoming(): bool
    {
        return $this->dateHeureDebut > new DateTime;
    }

    public function getPrixNormal(): ?\Money\Money
    {
        return $this->tarification->getPrixNormal();
    }

    public function getPrixReduit(): ?\Money\Money
    {
        return $this->tarification->getPrixReduit();
    }

    public function getPrixSenior(): ?\Money\Money
    {
        return $this->tarification->getPrixSenior();
    }

    public function getPrixEnfant(): ?\Money\Money
    {
        return $this->tarification->getPrixEnfant();
    }

    public function getPrixPMR(): ?\Money\Money
    {
        return $this->tarification->getPrixPMR();
    }

    public function getTarification(): Tarification
    {
        return $this->tarification;
    }

    public function changerStatut(StatutSeance $nouveauStatut): void
    {
        $this->statut = $nouveauStatut;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function reporter(DateTimeInterface $nouvelleHeureDebut, DateTimeInterface $nouvelleHeureFin): void
    {
        $this->dateHeureDebut = $nouvelleHeureDebut;
        $this->dateHeureFin   = $nouvelleHeureFin;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function annuler(): void
    {
        $this->statut = StatutSeance::ANNULEE;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function getFormattedTime(): string
    {
        return $this->dateHeureDebut->format('H:i');
    }

    public function getSalleType(): string
    {
        return $this->placementLibre ? 'libre' : 'numerotee';
    }

    public function getPlacesDisponibles(): int
    {

        // Pour l'instant, retourne une valeur par défaut
        return 50; // Capacité temporaire
    }

    /**
     * @return array<string>
     */
    public function getPlacesOccupees(): array
    {

        // Pour l'instant, retourne un array vide (aucune place occupée)
        return []; // Temporaire
    }

    /**
     * @return array<string, mixed>
     */
    public function getSalleConfiguration(): array
    {

        // Pour l'instant, retourne une configuration temporaire
        return [
            'rangees'           => ['A', 'B', 'C', 'D'],
            'places_par_rangee' => 10,
            'total_places'      => 40,
        ];
    }

    // Méthodes de mise à jour pour le handler UpdateSeance
    public function updateDateHeureDebut(DateTimeInterface $nouvelleHeureDebut): void
    {
        $this->dateHeureDebut = $nouvelleHeureDebut;
        // Recalculer la fin en fonction de la durée du film
        $dureeMinutes = 120; // Par défaut, à ajuster selon le film
        $fin          = DateTime::createFromInterface($nouvelleHeureDebut);
        $fin->modify("+{$dureeMinutes} minutes");
        $this->dateHeureFin = $fin;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateDateHeureDebutAvecFin(DateTimeInterface $nouvelleHeureDebut, DateTimeInterface $nouvelleHeureFin): void
    {
        $this->dateHeureDebut = $nouvelleHeureDebut;
        $this->dateHeureFin   = $nouvelleHeureFin;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateFilm(Film $film): void
    {
        $this->filmId = $film->id;
        // Recalculer la fin selon la nouvelle durée
        $dureeMinutes = $film->dureeMinutes + 30; // +30 min pour pub/nettoyage
        $fin          = DateTime::createFromInterface($this->dateHeureDebut);
        $fin->modify("+{$dureeMinutes} minutes");
        $this->dateHeureFin = $fin;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateSalle(Salle $salle): void
    {
        $this->salleId = $salle->id;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateVersion(string $version): void
    {
        $this->version = $version;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateDureeAdditionnelle(?int $dureeAdditionnelle): void
    {
        $this->dureeAdditionnelle = $dureeAdditionnelle;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateQualiteProjection(?QualiteProjection $qualiteProjection): void
    {
        $this->qualiteProjection = $qualiteProjection;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateQualiteSonore(?QualiteSonore $qualiteSonore): void
    {
        $this->qualiteSonore = $qualiteSonore;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    /**
     * @param array<string, mixed> $tarification
     */
    public function updateTarification(array $tarification): void
    {
        // Reconstruire la tarification à partir du tableau
        $this->tarification = Tarification::fromArray([
            Tarification::TARIFS_BASE          => $tarification,
            Tarification::SUPPLEMENTS_SPECIAUX => $this->tarification->supplementsSpeciaux,
            Tarification::REDUCTIONS_SPECIALES => $this->tarification->reductionsSpeciales,
        ]);

        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function updateStatut(StatutSeance $statut): void
    {
        $this->changerStatut($statut);
    }

    public function updatePlacementLibre(bool $placementLibre): void
    {
        $this->placementLibre = $placementLibre;
        $this->addDomainEvent(SeanceUpdated::fromSeance($this));
    }

    public function getDateHeureFin(): DateTimeInterface
    {
        return $this->dateHeureFin;
    }
}
