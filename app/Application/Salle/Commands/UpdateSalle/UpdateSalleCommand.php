<?php

declare(strict_types=1);

namespace App\Application\Salle\Commands\UpdateSalle;

use Respect\Validation\Validator as v;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateSalleCommand implements CommandInterface
{
    /**
     * @param array<QualiteProjection>|null $qualiteProjection
     * @param array<QualiteSonore>|null $qualiteSonore
     * @param array<string, mixed>|null $planSalle
     */
    public function __construct(
        public string $salleUuid,
        public ?string $nom = null,
        public ?int $capaciteTotale = null,
        public ?int $nombreRangees = null,
        public ?int $placesParRangee = null,
        public ?int $placesStandard = null,
        public ?int $placesPmr = null,
        public ?array $qualiteProjection = null,
        public ?array $qualiteSonore = null,
        public ?bool $accessibilitePmr = null,
        public ?bool $climatisation = null,
        public ?array $planSalle = null,
        public ?StatutSalle $statut = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation UUID (toujours valider dans la Command)
        try {
            v::uuidV7()->assert($this->salleUuid);
        } catch (ValidationException $e) {
            $errors['salleUuid'] = 'L\'UUID de la salle n\'est pas valide';
            // dd('UUID validation failed:', $e->getMessage());
        }

        // Validation nom (si fourni)
        if ($this->nom !== null) {
            try {
                v::stringType()->notEmpty()->length(1, 100)->assert($this->nom);
            } catch (ValidationException $e) {
                $errors['nom'] = 'Le nom doit contenir entre 1 et 100 caractères';
            }
        }

        // Validation capacité totale (si fournie)
        if ($this->capaciteTotale !== null) {
            try {
                v::intType()->min(1)->max(1000)->assert($this->capaciteTotale);
            } catch (ValidationException $e) {
                $errors['capaciteTotale'] = 'La capacité doit être entre 1 et 1000';
            }
        }

        // Validation nombre rangées (si fourni)
        if ($this->nombreRangees !== null) {
            try {
                v::intType()->min(1)->max(50)->assert($this->nombreRangees);
            } catch (ValidationException $e) {
                $errors['nombreRangees'] = 'Le nombre de rangées doit être entre 1 et 50';
            }
        }

        // Validation places par rangée (si fourni)
        if ($this->placesParRangee !== null) {
            try {
                v::intType()->min(1)->max(100)->assert($this->placesParRangee);
            } catch (ValidationException $e) {
                $errors['placesParRangee'] = 'Le nombre de places par rangée doit être entre 1 et 100';
            }
        }

        // Validation places standard (si fourni)
        if ($this->placesStandard !== null) {
            try {
                v::intType()->min(0)->max(1000)->assert($this->placesStandard);
            } catch (ValidationException $e) {
                $errors['placesStandard'] = 'Le nombre de places standard doit être entre 0 et 1000';
            }
        }

        // Validation places PMR (si fourni)
        if ($this->placesPmr !== null) {
            try {
                v::intType()->min(0)->max(100)->assert($this->placesPmr);
            } catch (ValidationException $e) {
                $errors['placesPmr'] = 'Le nombre de places PMR doit être entre 0 et 100';
            }
        }

        // Validation qualités projection (si fournies)
        if ($this->qualiteProjection !== null && !empty($this->qualiteProjection)) {
            try {
                v::arrayType()->each(
                    v::callback(fn ($value) => $value instanceof QualiteProjection)
                )->assert($this->qualiteProjection);
            } catch (ValidationException $e) {
                $errors['qualiteProjection'] = 'Qualités de projection invalides';
            }
        }

        // Validation qualités sonores (si fournies)
        if ($this->qualiteSonore !== null && !empty($this->qualiteSonore)) {
            try {
                v::arrayType()->each(
                    v::callback(fn ($value) => $value instanceof QualiteSonore)
                )->assert($this->qualiteSonore);
            } catch (ValidationException $e) {
                $errors['qualiteSonore'] = 'Qualités sonores invalides';
            }
        }

        // Validation plan salle (si fourni)
        if ($this->planSalle !== null) {
            try {
                v::arrayType()->assert($this->planSalle);
            } catch (ValidationException $e) {
                $errors['planSalle'] = 'Plan de salle invalide';
            }
        }

        dump('UpdateSalleCommand validation errors:', $errors);

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function hasUpdates(): bool
    {
        return $this->nom !== null ||
               $this->capaciteTotale !== null ||
               $this->nombreRangees !== null ||
               $this->placesParRangee !== null ||
               $this->placesStandard !== null ||
               $this->placesPmr !== null ||
               $this->qualiteProjection !== null ||
               $this->qualiteSonore !== null ||
               $this->accessibilitePmr !== null ||
               $this->climatisation !== null ||
               $this->planSalle !== null ||
               $this->statut !== null;
    }
}
