<?php

declare(strict_types=1);

namespace App\Domain\User\Entities;

use DateTime;
use DateTimeInterface;
use App\Domain\Shared\ValueObjects\Nom;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\Prenom;
use App\Domain\Shared\Entities\AggregateRoot;
use App\Domain\User\ValueObjects\UserProfilId;
use App\Domain\Shared\ValueObjects\PhoneNumber;

/**
 * @property UserProfilId $id
 * @property UserId $userId
 * @property Prenom $prenom
 * @property Nom $nom
 * @property DateTimeInterface|null $dateNaissance
 * @property PhoneNumber|null $telephone
 * @property array<string, mixed>|null $adresse
 * @property array<string, mixed>|null $preferences
 * @property bool $newsletter
 * @property string $email
 */
final class UserProfil extends AggregateRoot
{
    public readonly UserProfilId $id;

    public private(set) UserId $userId;

    public private(set) Prenom $prenom;

    public private(set) Nom $nom;

    public private(set) ?DateTimeInterface $dateNaissance;

    public private(set) ?PhoneNumber $telephone;

    /**
     * @var array<string, mixed>|null
     */
    public private(set) ?array $adresse;

    /**
     * @var array<string, mixed>|null
     */
    public private(set) ?array $preferences;

    public private(set) bool $newsletter;

    /**
     * @param array<string, mixed>|null $adresse
     * @param array<string, mixed>|null $preferences
     */
    public function __construct(
        UserProfilId $id,
        UserId $userId,
        Prenom $prenom,
        Nom $nom,
        ?DateTimeInterface $dateNaissance = null,
        ?PhoneNumber $telephone = null,
        ?array $adresse = null,
        ?array $preferences = null,
        bool $newsletter = false,
    ) {
        $this->id            = $id;
        $this->userId        = $userId;
        $this->prenom        = $prenom;
        $this->nom           = $nom;
        $this->dateNaissance = $dateNaissance;
        $this->telephone     = $telephone;
        $this->adresse       = $adresse;
        $this->preferences   = $preferences;
        $this->newsletter    = $newsletter;
    }

    /**
     * @param array<string, mixed>|null $adresse
     */
    public static function creer(
        UserId $userId,
        Prenom $prenom,
        Nom $nom,
        ?DateTimeInterface $dateNaissance = null,
        ?PhoneNumber $telephone = null,
        ?array $adresse = null,
        bool $newsletter = false,
    ): self {
        return new self(
            UserProfilId::generate(),
            $userId,
            $prenom,
            $nom,
            $dateNaissance,
            $telephone,
            $adresse,
            null, // preferences
            $newsletter
        );
    }

    public function getFullName(): string
    {
        return $this->prenom->toString() . ' ' . $this->nom->toString();
    }

    public function getAge(): ?int
    {
        if (!$this->dateNaissance) {
            return null;
        }

        return (int) $this->dateNaissance->diff(new DateTime)->format('%y');
    }

    public function changerNom(Nom $nouveauNom): void
    {
        $this->nom = $nouveauNom;
    }

    public function changerPrenom(Prenom $nouveauPrenom): void
    {
        $this->prenom = $nouveauPrenom;
    }

    public function changerDateNaissance(?DateTimeInterface $nouvelleDateNaissance): void
    {
        $this->dateNaissance = $nouvelleDateNaissance;
    }

    public function changerTelephone(?PhoneNumber $nouveauTelephone): void
    {
        $this->telephone = $nouveauTelephone;
    }

    /**
     * @param array<string, mixed>|null $nouvelleAdresse
     */
    public function changerAdresse(?array $nouvelleAdresse): void
    {
        $this->adresse = $nouvelleAdresse;
    }

    public function sAbonnerNewsletter(): void
    {
        $this->newsletter = true;
    }

    public function seDesabonnerNewsletter(): void
    {
        $this->newsletter = false;
    }

    /**
     * @param array<string, mixed>|null $nouvellesPreferences
     */
    public function changerPreferences(?array $nouvellesPreferences): void
    {
        $this->preferences = $nouvellesPreferences;
    }

    public function getVille(): ?string
    {
        return $this->adresse['ville'] ?? null;
    }

    public function getCodePostal(): ?string
    {
        return $this->adresse['code_postal'] ?? null;
    }

    public function getPays(): ?string
    {
        return $this->adresse['pays'] ?? null;
    }
}
