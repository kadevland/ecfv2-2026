<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\UpdateCinema;

use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateCinemaCommand implements CommandInterface
{
    /**
     * @param array<string, mixed>|null $horaires
     */
    public function __construct(
        public string $cinemaUuid,
        public ?string $nom = null,
        public ?string $pays = null,
        public ?string $rue = null,
        public ?string $ville = null,
        public ?string $codePostal = null,
        public ?string $telephone = null,
        public ?string $email = null,
        public ?string $description = null,
        public ?bool $estActif = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?array $horaires = null,
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

        // UUID validation removed - comes from route parameter, already validated by Laravel

        // Validation nom (si fourni)
        if ($this->nom !== null) {
            try {
                v::stringType()->notEmpty()->length(2, 100)->assert($this->nom);
            } catch (ValidationException $e) {
                $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères';
            }
        }

        // Validation pays (si fourni)
        if ($this->pays !== null) {
            try {
                v::stringType()->notEmpty()->length(2, 2)->assert($this->pays);
            } catch (ValidationException $e) {
                $errors['pays'] = 'Le code pays doit être un code ISO de 2 caractères';
            }
        }

        // Validation rue (si fournie)
        if ($this->rue !== null) {
            try {
                v::stringType()->notEmpty()->length(5, 200)->assert($this->rue);
            } catch (ValidationException $e) {
                $errors['rue'] = 'L\'adresse doit contenir entre 5 et 200 caractères';
            }
        }

        // Validation ville (si fournie)
        if ($this->ville !== null) {
            try {
                v::stringType()->notEmpty()->length(2, 100)->assert($this->ville);
            } catch (ValidationException $e) {
                $errors['ville'] = 'La ville doit contenir entre 2 et 100 caractères';
            }
        }

        // Validation code postal (si fourni)
        if ($this->codePostal !== null) {
            try {
                v::stringType()->notEmpty()->length(4, 10)->assert($this->codePostal);
            } catch (ValidationException $e) {
                $errors['codePostal'] = 'Le code postal doit contenir entre 4 et 10 caractères';
            }
        }

        // Validation téléphone (si fourni)
        if ($this->telephone !== null && $this->telephone !== '') {
            try {
                v::stringType()->phone()->assert($this->telephone);
            } catch (ValidationException $e) {
                $errors['telephone'] = 'Le numéro de téléphone n\'est pas valide';
            }
        }

        // Validation email (si fourni)
        if ($this->email !== null && $this->email !== '') {
            try {
                v::stringType()->email()->assert($this->email);
            } catch (ValidationException $e) {
                $errors['email'] = 'L\'adresse email n\'est pas valide';
            }
        }

        // Validation description (si fournie)
        if ($this->description !== null) {
            try {
                v::stringType()->length(null, 1000)->assert($this->description);
            } catch (ValidationException $e) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            }
        }

        // Validation coordonnées GPS (si fournies)
        if ($this->latitude !== null) {
            try {
                v::floatType()->min(-90)->max(90)->assert($this->latitude);
            } catch (ValidationException $e) {
                $errors['latitude'] = 'La latitude doit être comprise entre -90 et 90';
            }
        }

        if ($this->longitude !== null) {
            try {
                v::floatType()->min(-180)->max(180)->assert($this->longitude);
            } catch (ValidationException $e) {
                $errors['longitude'] = 'La longitude doit être comprise entre -180 et 180';
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
