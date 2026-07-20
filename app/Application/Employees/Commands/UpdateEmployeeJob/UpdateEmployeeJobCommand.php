<?php

declare(strict_types=1);

namespace App\Application\Employees\Commands\UpdateEmployeeJob;

use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateEmployeeJobCommand implements CommandInterface
{
    public function __construct(
        public string $userUuid,
        public ?string $titrePoste = null,
        public ?string $description = null,
        public ?string $categorie = null,
        public ?string $niveau = null,
        public ?string $typeContrat = null,
        public ?string $tempsTravail = null,
        public ?string $cinemaId = null,
        public ?float $salaireMensuel = null,
        public ?string $dateEmbauche = null,
        public ?bool $encadrementEquipe = null,
        public ?int $nombrePersonnesEncadrees = null,
        public ?bool $travailWeekend = null,
        public ?bool $travailFeries = null,
        public ?bool $travailSoiree = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation UUID utilisateur
        try {
            v::stringType()->notEmpty()
                ->uuidV7()
                ->assert($this->userUuid);
        } catch (ValidationException $e) {
            $errors['userUuid'] = 'L\'UUID de l\'utilisateur n\'est pas valide';
        }

        // Validation titre poste
        if ($this->titrePoste !== null) {
            try {
                v::stringType()->notEmpty()
                    ->length(2, 100)
                    ->assert($this->titrePoste);
            } catch (ValidationException $e) {
                $errors['titrePoste'] = 'Le titre du poste doit contenir entre 2 et 100 caractères';
            }
        }

        // Validation catégorie
        if ($this->categorie !== null) {
            try {
                v::in([
                    'DIRECTION', 'ENCADREMENT', 'ACCUEIL_BILLETTERIE',
                    'PROJECTION', 'ENTRETIEN', 'SECURITE', 'TECHNIQUE',
                    'ADMINISTRATIF', 'ANIMATION', 'RESTAURATION',
                ])->assert($this->categorie);
            } catch (ValidationException $e) {
                $errors['categorie'] = 'La catégorie doit être valide';
            }
        }

        // Validation niveau
        if ($this->niveau !== null) {
            try {
                v::in([
                    'STAGIAIRE', 'JUNIOR', 'CONFIRME', 'SENIOR',
                    'EXPERT', 'RESPONSABLE', 'MANAGER', 'DIRECTEUR',
                ])->assert($this->niveau);
            } catch (ValidationException $e) {
                $errors['niveau'] = 'Le niveau doit être valide';
            }
        }

        // Validation type contrat
        if ($this->typeContrat !== null) {
            try {
                v::in([
                    'CDI', 'CDD', 'INTERIM', 'STAGE', 'APPRENTISSAGE', 'FREELANCE',
                ])->assert($this->typeContrat);
            } catch (ValidationException $e) {
                $errors['typeContrat'] = 'Le type de contrat doit être valide';
            }
        }

        // Validation temps travail
        if ($this->tempsTravail !== null) {
            try {
                v::in([
                    'TEMPS_PLEIN', 'TEMPS_PARTIEL', 'HORAIRES_VARIABLES', 'SAISONNIER',
                ])->assert($this->tempsTravail);
            } catch (ValidationException $e) {
                $errors['tempsTravail'] = 'Le temps de travail doit être valide';
            }
        }

        // Validation cinéma UUID
        if ($this->cinemaId !== null) {
            try {
                v::uuidV7()->assert($this->cinemaId);
            } catch (ValidationException $e) {
                $errors['cinemaId'] = 'L\'UUID du cinéma n\'est pas valide';
            }
        }

        // Validation salaire
        if ($this->salaireMensuel !== null) {
            try {
                v::floatType()->min(0)->assert($this->salaireMensuel);
            } catch (ValidationException $e) {
                $errors['salaireMensuel'] = 'Le salaire doit être un nombre positif';
            }
        }

        // Validation date embauche
        if ($this->dateEmbauche !== null) {
            try {
                v::date('Y-m-d')->assert($this->dateEmbauche);
            } catch (ValidationException $e) {
                $errors['dateEmbauche'] = 'La date d\'embauche doit être une date valide';
            }
        }

        // Validation nombre personnes encadrées
        if ($this->nombrePersonnesEncadrees !== null) {
            try {
                v::intType()->min(0)->assert($this->nombrePersonnesEncadrees);
            } catch (ValidationException $e) {
                $errors['nombrePersonnesEncadrees'] = 'Le nombre de personnes encadrées doit être un entier positif';
            }
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
