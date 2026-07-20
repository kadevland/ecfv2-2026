<?php

declare(strict_types=1);

namespace App\Application\Seance\Commands\UpdateSeance;

use Carbon\CarbonImmutable;
use App\Domain\Enums\VersionFilm;
use App\Domain\Enums\StatutSeance;
use Respect\Validation\Validator as v;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateSeanceCommand implements CommandInterface
{
    /**
     * @param array<string, mixed>|null $tarification
     */
    public function __construct(
        public string $seanceUuid,
        public ?string $dateHeureDebut = null,
        public ?int $dureeAdditionnelle = null,
        public ?string $filmUuid = null,
        public ?string $salleUuid = null,
        public ?string $version = null,
        public ?array $tarification = null,
        public ?bool $placementLibre = null,
        public ?string $qualiteProjection = null,
        public ?string $qualiteSonore = null,
        public ?string $statut = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation UUID
        try {
            v::uuidV7()->assert($this->seanceUuid);
        } catch (ValidationException $e) {
            $errors['seanceUuid'] = 'L\'UUID de la séance n\'est pas valide';
        }

        // Validation date heure début (si fournie)
        if ($this->dateHeureDebut !== null) {
            try {
                v::callback(fn ($value) => CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value) instanceof CarbonImmutable)
                    ->assert($this->dateHeureDebut);
            } catch (ValidationException $e) {
                $errors['dateHeureDebut'] = 'Format de date/heure invalide (YYYY-MM-DD HH:MM:SS)';
            }
        }

        // Validation durée additionnelle (si fournie)
        if ($this->dureeAdditionnelle !== null) {
            try {
                v::intType()->between(10, 60)->assert($this->dureeAdditionnelle);
            } catch (ValidationException $e) {
                $errors['dureeAdditionnelle'] = 'La durée additionnelle doit être entre 10 et 60 minutes';
            }
        }

        // Validation film UUID (si fourni)
        if ($this->filmUuid !== null) {
            try {
                v::uuidV7()->assert($this->filmUuid);
            } catch (ValidationException $e) {
                $errors['filmUuid'] = 'L\'UUID du film n\'est pas valide';
            }
        }

        // Validation salle UUID (si fourni)
        if ($this->salleUuid !== null) {
            try {
                v::uuidV7()->assert($this->salleUuid);
            } catch (ValidationException $e) {
                $errors['salleUuid'] = 'L\'UUID de la salle n\'est pas valide';
            }
        }

        // Validation version (si fournie)
        if ($this->version !== null) {
            try {
                $enumValues = array_map(fn ($case) => $case->value, VersionFilm::cases());
                v::stringType()->in($enumValues)->assert($this->version);
            } catch (ValidationException $e) {
                $errors['version'] = 'Version invalide (' . implode(', ', array_map(fn ($case) => $case->value, VersionFilm::cases())) . ')';
            }
        }

        // Validation qualité projection (si fournie)
        if ($this->qualiteProjection !== null) {
            try {
                $valeursProjection = array_map(fn ($case) => $case->value, QualiteProjection::cases());
                v::stringType()->in($valeursProjection)->assert($this->qualiteProjection);
            } catch (ValidationException $e) {
                $errors['qualiteProjection'] = 'Qualité de projection invalide';
            }
        }

        // Validation qualité sonore (si fournie)
        if ($this->qualiteSonore !== null) {
            try {
                $valeursSonore = array_map(fn ($case) => $case->value, QualiteSonore::cases());
                v::stringType()->in($valeursSonore)->assert($this->qualiteSonore);
            } catch (ValidationException $e) {
                $errors['qualiteSonore'] = 'Qualité sonore invalide';
            }
        }

        // Validation tarification (si fournie)
        if ($this->tarification !== null) {
            try {
                v::arrayType()->notEmpty()->assert($this->tarification);

            } catch (ValidationException $e) {
                $errors['tarification'] = 'Structure de tarification invalide';
            }
        }

        // Validation statut (si fourni)
        if ($this->statut !== null) {
            try {
                $enumValues = array_map(fn ($case) => $case->value, StatutSeance::cases());
                v::stringType()->in($enumValues)->assert($this->statut);
            } catch (ValidationException $e) {
                $errors['statut'] = 'Statut invalide (' . implode(', ', array_map(fn ($case) => $case->value, StatutSeance::cases())) . ')';
            }
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function hasUpdates(): bool
    {
        return $this->dateHeureDebut !== null ||
               $this->dureeAdditionnelle !== null ||
               $this->filmUuid !== null ||
               $this->salleUuid !== null ||
               $this->version !== null ||
               $this->tarification !== null ||
               $this->placementLibre !== null ||
               $this->qualiteProjection !== null ||
               $this->qualiteSonore !== null ||
               $this->statut !== null;
    }
}
