<?php

declare(strict_types=1);

namespace App\Application\Seance\Commands\CreateSeance;

use Carbon\CarbonImmutable;
use App\Domain\Enums\StatutSeance;
use Respect\Validation\Validator as v;
use App\Domain\Shared\Enums\DeviseEnum;
use App\Domain\Cinema\Enums\VersionFilmEnum;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class CreateSeanceCommand implements CommandInterface
{
    /**
     * @param array<string, float> $tarifsBase
     * @param array<string, mixed>|null $optionsSupplementaires
     * @param array<string, mixed>|null $supplementsSpeciaux
     * @param array<string, mixed>|null $reductionsSpeciales
     */
    public function __construct(
        public string $filmUuid,
        public string $salleUuid,
        public string $dateHeureDebut,
        public ?string $dateHeureFin,
        public string $version,
        public array $tarifsBase,
        public float $tauxTva,
        public int $dureeAdditionnelle = 30,
        public string $devise = 'EUR',
        public bool $placementLibre = false,
        public string $statut = 'programmee',
        public ?array $optionsSupplementaires = null,
        public ?array $supplementsSpeciaux = null,
        public ?array $reductionsSpeciales = null,
    ) {}

    /**
     * Valide les données de la command
     *
     * @return array<string, string> Tableau des erreurs (vide si valide)
     */
    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation filmUuid (UUID v7)
        try {
            v::uuidV7()->assert($this->filmUuid);
        } catch (ValidationException $e) {
            $errors['filmUuid'] = 'UUID du film invalide';
        }

        // Validation salleUuid (UUID v7)
        try {
            v::uuidV7()->assert($this->salleUuid);
        } catch (ValidationException $e) {
            $errors['salleUuid'] = 'UUID de la salle invalide';
        }

        // Validation dateHeureDebut
        try {
            v::callback(fn ($value) => CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value) instanceof CarbonImmutable)
                ->assert($this->dateHeureDebut);
        } catch (ValidationException $e) {
            $errors['dateHeureDebut'] = 'Format de date/heure de début invalide (YYYY-MM-DD HH:MM:SS)';
        }

        // Validation dateHeureFin (optionnelle - calculée par le handler si null)
        if ($this->dateHeureFin !== null) {
            try {
                v::callback(fn ($value) => CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value) instanceof CarbonImmutable)
                    ->assert($this->dateHeureFin);
            } catch (ValidationException $e) {
                $errors['dateHeureFin'] = 'Format de date/heure de fin invalide (YYYY-MM-DD HH:MM:SS)';
            }

            // Validation que la date de fin est après la date de début seulement si fournie
            if (empty($errors['dateHeureDebut']) && empty($errors['dateHeureFin'])) {
                $debut = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $this->dateHeureDebut);
                $fin   = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $this->dateHeureFin);

                if ($debut && $fin && $fin <= $debut) {
                    $errors['dateHeureFin'] = 'La date de fin doit être postérieure à la date de début';
                }
            }
        }

        // Validation dureeAdditionnelle
        if ($this->dureeAdditionnelle < 0 || $this->dureeAdditionnelle > 180) {
            $errors['dureeAdditionnelle'] = 'La durée additionnelle doit être entre 0 et 180 minutes';
        }

        // Validation version
        try {
            v::stringType()->in(VersionFilmEnum::values())->assert($this->version);
        } catch (ValidationException $e) {
            $errors['version'] = 'Version invalide (' . implode(', ', VersionFilmEnum::values()) . ')';
        }

        // Validation tarifsBase
        try {
            v::arrayType()->notEmpty()->assert($this->tarifsBase);
            foreach ($this->tarifsBase as $type => $prix) {
                v::stringType()->notEmpty()->assert($type);
                v::floatType()->min(0)->max(100)->assert((float) $prix);
            }
        } catch (ValidationException $e) {
            $errors['tarifsBase'] = 'Tarifs de base invalides';
        }

        // Validation tauxTva
        try {
            v::floatType()->min(0)->max(100)->assert($this->tauxTva);
        } catch (ValidationException $e) {
            $errors['tauxTva'] = 'Le taux TVA doit être entre 0 et 100%';
        }

        // Validation devise
        try {
            v::stringType()->in(DeviseEnum::values())->assert($this->devise);
        } catch (ValidationException $e) {
            $errors['devise'] = 'Devise non supportée (' . implode(', ', DeviseEnum::values()) . ')';
        }

        // Validation statut
        try {
            $enumValues = array_map(fn ($case) => $case->value, StatutSeance::cases());
            v::stringType()->in($enumValues)->assert($this->statut);
        } catch (ValidationException $e) {
            $errors['statut'] = 'Statut invalide (' . implode(', ', array_map(fn ($case) => $case->value, StatutSeance::cases())) . ')';
        }

        // Validation supplementsSpeciaux (optionnel)
        if ($this->supplementsSpeciaux !== null) {
            try {
                v::arrayType()->assert($this->supplementsSpeciaux);
                foreach ($this->supplementsSpeciaux as $type => $prix) {
                    v::stringType()->notEmpty()->assert($type);
                    v::floatType()->min(0)->assert((float) $prix);
                }
            } catch (ValidationException $e) {
                $errors['supplementsSpeciaux'] = 'Suppléments spéciaux invalides';
            }
        }

        // Validation reductionsSpeciales (optionnel)
        if ($this->reductionsSpeciales !== null) {
            try {
                v::arrayType()->assert($this->reductionsSpeciales);
                foreach ($this->reductionsSpeciales as $type => $prix) {
                    v::stringType()->notEmpty()->assert($type);
                    v::floatType()->min(0)->assert((float) $prix);
                }
            } catch (ValidationException $e) {
                $errors['reductionsSpeciales'] = 'Réductions spéciales invalides';
            }
        }

        return $errors;
    }

    /**
     * Vérifie si la command est valide
     */
    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
