<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Entities;

use App\Domain\Shared\Enums\CodePays;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Cinema\Events\CinemaCreated;
use App\Domain\Cinema\Events\CinemaUpdated;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\Entities\AggregateRoot;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Domain\Shared\ValueObjects\HorairesOuverture;

/**
 * @property CinemaId $id
 * @property string $nom
 * @property Address $adresse
 * @property CodePays $pays
 * @property PhoneNumber|null $telephone
 * @property Email|null $email
 * @property bool $estActif
 * @property string|null $description
 * @property CoordonneesGps|null $coordonneesGps
 * @property HorairesOuverture|null $horairesOuverture
 */
final class Cinema extends AggregateRoot
{
    public readonly CinemaId $id;

    public private(set) string $nom;

    public private(set) Address $adresse;

    public private(set) CodePays $pays;

    public private(set) ?PhoneNumber $telephone;

    public private(set) ?Email $email;

    public private(set) bool $estActif;

    public private(set) ?string $description;

    public private(set) ?CoordonneesGps $coordonneesGps;

    public private(set) ?HorairesOuverture $horairesOuverture;

    public function __construct(
        CinemaId $id,
        string $nom,
        Address $adresse,
        CodePays $pays,
        ?PhoneNumber $telephone = null,
        ?Email $email = null,
        bool $estActif = true,
        ?string $description = null,
        ?CoordonneesGps $coordonneesGps = null,
        ?HorairesOuverture $horairesOuverture = null,
    ) {
        $this->id                = $id;
        $this->nom               = $nom;
        $this->adresse           = $adresse;
        $this->pays              = $pays;
        $this->telephone         = $telephone;
        $this->email             = $email;
        $this->estActif          = $estActif;
        $this->description       = $description;
        $this->coordonneesGps    = $coordonneesGps;
        $this->horairesOuverture = $horairesOuverture;
    }

    public static function creer(
        string $nom,
        Address $adresse,
        CodePays $pays,
        ?PhoneNumber $telephone = null,
        ?Email $email = null,
        ?string $description = null,
        ?CoordonneesGps $coordonneesGps = null,
        ?HorairesOuverture $horairesOuverture = null
    ): self {
        $cinema = new self(
            CinemaId::generate(),
            $nom,
            $adresse,
            $pays,
            $telephone,
            $email,
            true,
            $description,
            $coordonneesGps,
            $horairesOuverture
        );

        $cinema->addDomainEvent(CinemaCreated::fromCinema($cinema));

        return $cinema;
    }

    public function getAdresseComplete(): string
    {
        return $this->adresse->getAdresseComplete();
    }

    public function changerNom(string $nouveauNom): void
    {
        $this->nom = $nouveauNom;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function changerAdresse(Address $nouvelleAdresse, CodePays $nouveauPays): void
    {
        $this->adresse = $nouvelleAdresse;
        $this->pays    = $nouveauPays;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function changerContact(?PhoneNumber $nouveauTelephone, ?Email $nouvelEmail): void
    {
        $this->telephone = $nouveauTelephone;
        $this->email     = $nouvelEmail;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function activer(): void
    {
        $this->estActif = true;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function desactiver(): void
    {
        $this->estActif = false;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function changerDescription(?string $nouvelleDescription): void
    {
        $this->description = $nouvelleDescription;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function changerCoordonneesGps(?CoordonneesGps $nouvellesCoordonnees): void
    {
        $this->coordonneesGps = $nouvellesCoordonnees;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function changerHorairesOuverture(?HorairesOuverture $nouveauxHoraires): void
    {
        $this->horairesOuverture = $nouveauxHoraires;
        $this->addDomainEvent(CinemaUpdated::fromCinema($this));
    }

    public function getDistanceVers(Cinema $autreCinema): ?float
    {
        if ($this->coordonneesGps === null || $autreCinema->coordonneesGps === null) {
            return null;
        }

        return $this->coordonneesGps->distanceVers($autreCinema->coordonneesGps);
    }

    public function getGoogleMapsUrl(): ?string
    {
        return $this->coordonneesGps?->getGoogleMapsUrl();
    }
}
