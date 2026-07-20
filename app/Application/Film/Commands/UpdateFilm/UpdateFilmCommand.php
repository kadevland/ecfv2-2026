<?php

declare(strict_types=1);

namespace App\Application\Film\Commands\UpdateFilm;

use Carbon\CarbonImmutable;
use Respect\Validation\Validator as v;
use App\Domain\Cinema\Enums\StatusFilmEnum;
use App\Application\Contracts\CommandInterface;
use App\Domain\Cinema\Enums\ClassificationFilmEnum;
use App\Domain\Enums\GenreFilm;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateFilmCommand implements CommandInterface
{
    /**
     * @param string[] $realisateurs
     * @param string[] $imagesAdditionnelles
     * @param array<string, mixed> $metadonneesTechniques
     */
    public function __construct (
        public string $filmUuid,
        public ?string $titre = null,
        public ?string $titreOriginal = null,
        public ?string $synopsis = null,
        public string|array|null $genre = null,
        /** @var string[]|null */ public ?array $realisateurs = null,
        public ?array $acteursPrincipaux = null,
        public ?int $dureeMinutes = null,
        public ?string $dateSortie = null,
        public ?string $paysOrigine = null,
        public ?string $langueOriginale = null,
        public ?string $classification = null,
        public ?string $producteur = null,
        public ?string $afficheUrl = null,
        public ?string $bandeAnnonceUrl = null,
        /** @var string[]|null */ public ?array $imagesAdditionnelles = null,
        public ?float $noteCritique = null,
        public ?float $notePublic = null,
        public ?string $statut = null,
        public ?bool $estActif = null,
        /** @var array<string, mixed>|null */ public ?array $metadonneesTechniques = null,
    ) {}

    /**
     * Valide les données de la command
     *
     * @return array<string, string> Tableau des erreurs (vide si valide)
     */
    /**
     * @return array<string, string>
     */
    public function validate () : array
    {
        $errors = [];

        // Validation UUID
        try {
            v::uuidV7()->assert($this->filmUuid);
        } catch (ValidationException $e) {
            $errors['filmUuid'] = 'L\'UUID du film n\'est pas valide';
        }

        // Validation titre (si fourni)
        if ($this->titre !== null) {
            try {
                v::stringType()->notEmpty()
                    ->length(1, 200)
                    ->assert($this->titre);
            } catch (ValidationException $e) {
                $errors['titre'] = 'Le titre doit contenir entre 1 et 200 caractères';
            }
        }

        // Validation titre original (si fourni)
        if ($this->titreOriginal !== null && $this->titreOriginal !== '') {
            try {
                v::stringType()->length(1, 300)
                    ->assert($this->titreOriginal);
            } catch (ValidationException $e) {
                $errors['titreOriginal'] = 'Le titre original doit contenir entre 1 et 300 caractères';
            }
        }

        // Validation réalisateurs (si fourni)
        if ($this->realisateurs !== null) {
            try {
                v::arrayType()->notEmpty()
                    ->each(
                        v::stringType()->notEmpty()
                            ->length(1, 100)
                    )
                    ->assert($this->realisateurs);
            } catch (ValidationException $e) {
                $errors['realisateurs'] = 'Au moins un réalisateur requis';
            }
        }

        if ($this->acteursPrincipaux !== null) {
            try {
                v::arrayType()->notEmpty()
                    ->each(
                        v::stringType()->notEmpty()
                            ->length(1, 100)
                    )
                    ->assert($this->acteursPrincipaux);
            } catch (ValidationException $e) {
                $errors['acteurs_principaux'] = 'Au moins un acteur requis';
            }
        }

        // Validation acteurs principaux (si fourni) - texte libre
        /*if ($this->acteursPrincipaux !== null && $this->acteursPrincipaux !== '') {
            try {
                v::stringType()->length(1, 1000)->assert($this->acteursPrincipaux);
            } catch (ValidationException $e) {
                $errors['acteursPrincipaux'] = 'Les acteurs principaux ne peuvent pas dépasser 1000 caractères';
            }
        }*/

        // Validation genre (si fourni) - un seul genre
        // if ($this->genre !== null) {
        //     try {
        //         v::stringType()->notEmpty()->length(1, 255)->assert($this->genre);
        //     } catch (ValidationException $e) {
        //         $errors['genre'] = 'Genre invalide';
        //     }
        // }

        if ($this->genre !== null) {
            try {
                v::arrayType()->notEmpty()
                    ->each(
                        v::stringType()->in(GenreFilm::values())
                    )
                    ->assert($this->genre);
            } catch (ValidationException $e) {
                $errors['genre'] = 'Genre invalide';
            }
        }

        // Validation synopsis (si fourni)
        if ($this->synopsis !== null && $this->synopsis !== '') {
            try {
                v::stringType()->length(1, 5000)
                    ->assert($this->synopsis);
            } catch (ValidationException $e) {
                $errors['synopsis'] = 'Le synopsis ne peut pas dépasser 5000 caractères';
            }
        }

        // Validation durée (si fournie)
        if ($this->dureeMinutes !== null) {
            try {
                v::intType()->min(1)
                    ->max(600)
                    ->assert($this->dureeMinutes);
            } catch (ValidationException $e) {
                $errors['dureeMinutes'] = 'La durée doit être entre 1 et 600 minutes';
            }
        }

        // Validation classification (si fournie)
        if ($this->classification !== null) {
            try {
                v::stringType()->in(ClassificationFilmEnum::values())
                    ->assert($this->classification);
            } catch (ValidationException $e) {
                $errors['classification'] = 'Classification invalide (' . implode(', ', ClassificationFilmEnum::values()) . ')';
            }
        }

        // Validation langue originale (si fournie)
        if ($this->langueOriginale !== null && $this->langueOriginale !== '') {
            try {
                v::stringType()->length(2, 50)
                    ->assert($this->langueOriginale);
            } catch (ValidationException $e) {
                $errors['langueOriginale'] = 'Langue originale invalide';
            }
        }

        // Validation pays origine (si fourni)
        if ($this->paysOrigine !== null && $this->paysOrigine !== '') {
            try {
                v::stringType()->length(2, 100)
                    ->assert($this->paysOrigine);
            } catch (ValidationException $e) {
                $errors['paysOrigine'] = 'Pays d\'origine invalide';
            }
        }

        // Validation producteur (si fourni)
        if ($this->producteur !== null && $this->producteur !== '') {
            try {
                v::stringType()->length(2, 200)
                    ->assert($this->producteur);
            } catch (ValidationException $e) {
                $errors['producteur'] = 'Nom du producteur invalide';
            }
        }

        // Validation statut (si fourni)
        if ($this->statut !== null) {
            try {
                v::stringType()->in(StatusFilmEnum::values())
                    ->assert($this->statut);
            } catch (ValidationException $e) {
                $errors['statut'] = 'Statut invalide (' . implode(', ', StatusFilmEnum::values()) . ')';
            }
        }

        // Validation date de sortie (si fournie)
        if ($this->dateSortie !== null) {
            try {
                v::callback(fn ($value) => CarbonImmutable::createFromFormat('Y-m-d', $value) instanceof CarbonImmutable)
                    ->assert($this->dateSortie);
            } catch (ValidationException $e) {
                $errors['dateSortie'] = 'Format de date de sortie invalide (YYYY-MM-DD)';
            }
        }

        // Validation images additionnelles (si fournies)
        if ($this->imagesAdditionnelles !== null) {
            try {
                v::arrayType()->each(
                    v::stringType()->url()
                )
                    ->assert($this->imagesAdditionnelles);
            } catch (ValidationException $e) {
                $errors['imagesAdditionnelles'] = 'URLs d\'images invalides';
            }
        }

        // Validation métadonnées techniques (si fournies)
        if ($this->metadonneesTechniques !== null) {
            try {
                v::arrayType()->assert($this->metadonneesTechniques);
            } catch (ValidationException $e) {
                $errors['metadonneesTechniques'] = 'Métadonnées techniques invalides';
            }
        }

        // Validation notes (si fournies)
        if ($this->noteCritique !== null) {
            try {
                v::floatType()->min(0)
                    ->max(10)
                    ->assert($this->noteCritique);
            } catch (ValidationException $e) {
                $errors['noteCritique'] = 'La note critique doit être entre 0 et 10';
            }
        }

        if ($this->notePublic !== null) {
            try {
                v::floatType()->min(0)
                    ->max(10)
                    ->assert($this->notePublic);
            } catch (ValidationException $e) {
                $errors['notePublic'] = 'La note public doit être entre 0 et 10';
            }
        }

        // Validation URLs (si fournies)
        if ($this->afficheUrl !== null && $this->afficheUrl !== '') {
            try {
                v::stringType()->url()
                    ->assert($this->afficheUrl);
            } catch (ValidationException $e) {
                $errors['afficheUrl'] = 'URL de l\'affiche invalide';
            }
        }

        if ($this->bandeAnnonceUrl !== null && $this->bandeAnnonceUrl !== '') {
            try {
                v::stringType()->url()
                    ->assert($this->bandeAnnonceUrl);
            } catch (ValidationException $e) {
                $errors['bandeAnnonceUrl'] = 'URL de la bande-annonce invalide';
            }
        }

        return $errors;
    }

    /**
     * Vérifie si la command est valide
     */
    public function isValid () : bool
    {
        return empty($this->validate());
    }

    /**
     * Vérifie si au moins un champ de mise à jour est fourni
     */
    public function hasUpdates () : bool
    {
        return $this->titre !== null ||
            $this->titreOriginal !== null ||
            $this->synopsis !== null ||
            $this->genre !== null ||
            $this->realisateurs !== null ||
            $this->acteursPrincipaux !== null ||
            $this->dureeMinutes !== null ||
            $this->dateSortie !== null ||
            $this->paysOrigine !== null ||
            $this->langueOriginale !== null ||
            $this->classification !== null ||
            $this->producteur !== null ||
            $this->afficheUrl !== null ||
            $this->bandeAnnonceUrl !== null ||
            $this->imagesAdditionnelles !== null ||
            $this->noteCritique !== null ||
            $this->notePublic !== null ||
            $this->statut !== null ||
            $this->estActif !== null ||
            $this->metadonneesTechniques !== null;
    }
}
