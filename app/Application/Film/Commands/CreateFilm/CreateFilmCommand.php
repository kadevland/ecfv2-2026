<?php

declare(strict_types=1);

namespace App\Application\Film\Commands\CreateFilm;

use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use App\Domain\Cinema\Enums\ClassificationFilmEnum;
use Respect\Validation\Exceptions\ValidationException;

final readonly class CreateFilmCommand implements CommandInterface
{
    public function __construct(
        public string $titre,
        /** @var array<string> */ public array $realisateurs,
        /** @var array<string> */ public array $genres,
        public int $dureeMinutes,
        public string $classification,
        public string $dateSortie,
        public ?string $titreFr = null,
        /** @var array<string> */ public array $acteursPrincipaux = [],
        public ?string $langueOriginale = null,
        public ?string $sousTitres = null,
        public ?string $resume = null,
        public ?string $dateFinExploitation = null,
        public ?float $notePresse = null,
        public ?float $notePublic = null,
        public ?string $afficheUrl = null,
        public ?string $bandeAnnonceUrl = null,
        public bool $estActif = true,
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

        // Validation titre
        try {
            v::stringType()->notEmpty()->length(1, 200)->assert($this->titre);
        } catch (ValidationException $e) {
            $errors['titre'] = 'Le titre doit contenir entre 1 et 200 caractères';
        }

        // Validation réalisateurs
        try {
            v::arrayType()->notEmpty()->each(v::stringType()->notEmpty())->assert($this->realisateurs);
        } catch (ValidationException $e) {
            $errors['realisateurs'] = 'Au moins un réalisateur doit être spécifié';
        }

        // Validation genres
        try {
            v::arrayType()->notEmpty()->each(v::stringType()->notEmpty())->assert($this->genres);
        } catch (ValidationException $e) {
            $errors['genres'] = 'Au moins un genre doit être spécifié';
        }

        // Validation durée
        try {
            v::intType()->min(1)->max(600)->assert($this->dureeMinutes);
        } catch (ValidationException $e) {
            $errors['dureeMinutes'] = 'La durée doit être entre 1 et 600 minutes';
        }

        // Validation classification
        try {
            v::stringType()->notEmpty()->in(ClassificationFilmEnum::values())->assert($this->classification);
        } catch (ValidationException $e) {
            $errors['classification'] = 'Classification invalide (' . implode(', ', ClassificationFilmEnum::values()) . ')';
        }

        // Validation date sortie
        try {
            v::date('Y-m-d')->assert($this->dateSortie);
        } catch (ValidationException $e) {
            $errors['dateSortie'] = 'Format de date invalide (YYYY-MM-DD)';
        }

        // Validation titre français (optionnel)
        if ($this->titreFr !== null) {
            try {
                v::stringType()->notEmpty()->length(1, 200)->assert($this->titreFr);
            } catch (ValidationException $e) {
                $errors['titreFr'] = 'Le titre français doit contenir entre 1 et 200 caractères';
            }
        }

        // Validation notes (optionnelles)
        if ($this->notePresse !== null) {
            try {
                v::floatType()->min(0)->max(10)->assert($this->notePresse);
            } catch (ValidationException $e) {
                $errors['notePresse'] = 'La note presse doit être entre 0 et 10';
            }
        }

        if ($this->notePublic !== null) {
            try {
                v::floatType()->min(0)->max(10)->assert($this->notePublic);
            } catch (ValidationException $e) {
                $errors['notePublic'] = 'La note public doit être entre 0 et 10';
            }
        }

        // Validation URLs (optionnelles)
        if ($this->afficheUrl !== null && $this->afficheUrl !== '') {
            try {
                v::url()->assert($this->afficheUrl);
            } catch (ValidationException $e) {
                $errors['afficheUrl'] = 'L\'URL de l\'affiche n\'est pas valide';
            }
        }

        if ($this->bandeAnnonceUrl !== null && $this->bandeAnnonceUrl !== '') {
            try {
                v::url()->assert($this->bandeAnnonceUrl);
            } catch (ValidationException $e) {
                $errors['bandeAnnonceUrl'] = 'L\'URL de la bande-annonce n\'est pas valide';
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
