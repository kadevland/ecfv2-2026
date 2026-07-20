<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\CreateCinema;

use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use Respect\Validation\Exceptions\ValidationException;

final readonly class CreateCinemaCommand implements CommandInterface
{
    public function __construct(
        public string $nom,
        public string $pays,
        public string $rue,
        public string $ville,
        public string $codePostal,
        public ?string $telephone = null,
        public ?string $email = null,
        public ?string $description = null,
        public bool $estActif = true,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?HorairesOuverture $horaires = null,
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

        // Validation nom
        try {
            v::stringType()->notEmpty()->length(2, 100)->assert($this->nom);
        } catch (ValidationException $e) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères';
        }

        // Validation pays (code ISO)
        try {
            v::stringType()->notEmpty()->length(2, 2)->assert($this->pays);
        } catch (ValidationException $e) {
            $errors['pays'] = 'Le code pays doit être un code ISO de 2 caractères';
        }

        // Validation rue
        try {
            v::stringType()->notEmpty()->length(5, 200)->assert($this->rue);
        } catch (ValidationException $e) {
            $errors['rue'] = 'L\'adresse doit contenir entre 5 et 200 caractères';
        }

        // Validation ville
        try {
            v::stringType()->notEmpty()->length(2, 100)->assert($this->ville);
        } catch (ValidationException $e) {
            $errors['ville'] = 'La ville doit contenir entre 2 et 100 caractères';
        }

        // Validation code postal
        try {
            v::stringType()->notEmpty()->length(4, 10)->assert($this->codePostal);
        } catch (ValidationException $e) {
            $errors['codePostal'] = 'Le code postal doit contenir entre 4 et 10 caractères';
        }

        // Validation téléphone (optionnel)
        if ($this->telephone !== null) {
            try {
                v::stringType()->notEmpty()->phone()->assert($this->telephone);
            } catch (ValidationException $e) {
                $errors['telephone'] = 'Le numéro de téléphone n\'est pas valide';
            }
        }

        // Validation email (optionnel)
        if ($this->email !== null) {
            try {
                v::stringType()->notEmpty()->email()->assert($this->email);
            } catch (ValidationException $e) {
                $errors['email'] = 'L\'adresse email n\'est pas valide';
            }
        }

        // Validation description (optionnel)
        if ($this->description !== null) {
            try {
                v::stringType()->length(null, 1000)->assert($this->description);
            } catch (ValidationException $e) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            }
        }

        // Validation coordonnées GPS (optionnelles)
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
