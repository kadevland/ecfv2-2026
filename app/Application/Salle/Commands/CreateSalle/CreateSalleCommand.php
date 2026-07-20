<?php

declare(strict_types=1);

namespace App\Application\Salle\Commands\CreateSalle;

use Respect\Validation\Validator as v;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class CreateSalleCommand implements CommandInterface
{
    /**
     * @param array<QualiteProjection> $qualiteProjection
     * @param array<QualiteSonore> $qualiteSonore
     * @param array<string, mixed>|null $planSalle
     */
    public function __construct(
        public string $cinemaUuid,
        public string $nom,
        public int $capaciteTotale,
        public int $nombreRangees,
        public int $placesParRangee,
        public int $placesStandard,
        public int $placesPmr,
        public array $qualiteProjection = [],
        public array $qualiteSonore = [],
        public bool $accessibilitePmr = false,
        public bool $climatisation = true,
        public ?array $planSalle = null,
        public StatutSalle $statut = StatutSalle::ACTIVE,
    ) {}

    /**
     * Valide les données de la command
     *
     * @return array<string, string> Tableau des erreurs (vide si valide)
     */
    public function validate(): array
    {
        $errors = [];

        // Validation cinemaUuid (UUID v7)
        try {
            v::uuidV7()->assert($this->cinemaUuid);
        } catch (ValidationException $e) {
            $errors['cinemaUuid'] = 'UUID du cinéma invalide';
        }

        // Validation nom
        try {
            v::stringType()->notEmpty()->length(1, 100)->assert($this->nom);
        } catch (ValidationException $e) {
            $errors['nom'] = 'Le nom doit contenir entre 1 et 100 caractères';
        }

        // Validation capacité totale
        try {
            v::intType()->min(1)->max(1000)->assert($this->capaciteTotale);
        } catch (ValidationException $e) {
            $errors['capaciteTotale'] = 'La capacité doit être entre 1 et 1000';
        }

        // Validation nombre rangées
        try {
            v::intType()->min(1)->max(50)->assert($this->nombreRangees);
        } catch (ValidationException $e) {
            $errors['nombreRangees'] = 'Le nombre de rangées doit être entre 1 et 50';
        }

        // Validation places par rangée
        try {
            v::intType()->min(1)->max(100)->assert($this->placesParRangee);
        } catch (ValidationException $e) {
            $errors['placesParRangee'] = 'Le nombre de places par rangée doit être entre 1 et 100';
        }

        // Validation places standard
        try {
            v::intType()->min(0)->max(1000)->assert($this->placesStandard);
        } catch (ValidationException $e) {
            $errors['placesStandard'] = 'Le nombre de places standard doit être entre 0 et 1000';
        }

        // Validation places PMR
        try {
            v::intType()->min(0)->max(100)->assert($this->placesPmr);
        } catch (ValidationException $e) {
            $errors['placesPmr'] = 'Le nombre de places PMR doit être entre 0 et 100';
        }

        // Validation qualités projection (optionnel)
        if (!empty($this->qualiteProjection)) {
            try {
                v::arrayType()->each(
                    v::callback(fn ($value) => $value instanceof QualiteProjection)
                )->assert($this->qualiteProjection);
            } catch (ValidationException $e) {
                $errors['qualiteProjection'] = 'Qualités de projection invalides';
            }
        }

        // Validation qualités sonores (optionnel)
        if (!empty($this->qualiteSonore)) {
            try {
                v::arrayType()->each(
                    v::callback(fn ($value) => $value instanceof QualiteSonore)
                )->assert($this->qualiteSonore);
            } catch (ValidationException $e) {
                $errors['qualiteSonore'] = 'Qualités sonores invalides';
            }
        }

        // Validation plan salle (optionnel)
        if ($this->planSalle !== null) {
            try {
                v::arrayType()->assert($this->planSalle);
            } catch (ValidationException $e) {
                $errors['planSalle'] = 'Plan de salle invalide';
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
