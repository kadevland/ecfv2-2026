<?php

declare(strict_types=1);

namespace Database\Factories\Cinema;

use App\Infrastructure\Database\Models\Cinema\Film;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;

/**
 * @extends Factory<Film>
 */
class FilmFactory extends Factory
{
    protected $model = Film::class;

    /**
     * @return array<string, mixed>
     *                              Aligned with Domain Entity + BDD Schema (23 fields)
     */
    public function definition(): array
    {
        $genresDisponibles = \App\Domain\Enums\GenreFilm::values();

        $classifications = \App\Domain\Cinema\Enums\ClassificationFilmEnum::values();

        $statuts = \App\Domain\Cinema\Enums\StatusFilmEnum::values();

        $sousTitresDisponibles = ['français', 'anglais', 'espagnol', 'allemand', 'italien'];

        return [
            // Domain Entity properties (toutes les 23 propriétés)
            FilmSchema::ID                    => \App\Domain\Cinema\ValueObjects\FilmId::generate(),
            FilmSchema::TITRE                 => $this->faker->sentence(3),
            FilmSchema::TITRE_ORIGINAL        => $this->faker->optional(0.7)->sentence(3),
            FilmSchema::SYNOPSIS              => $this->faker->optional(0.8)->paragraph(),
            FilmSchema::GENRES                => $this->faker->randomElements($genresDisponibles, rand(1, 3)), // Array comme Domain
            FilmSchema::DUREE_MINUTES         => $this->faker->numberBetween(80, 180),
            FilmSchema::CLASSIFICATION        => $this->faker->randomElement($classifications),
            FilmSchema::DATE_SORTIE           => $this->faker->dateTimeBetween('-2 years', '+6 months'),
            FilmSchema::DATE_FIN_EXPLOITATION => $this->faker->optional(0.3)->dateTimeBetween('+1 months', '+2 years'),
            FilmSchema::PAYS_ORIGINE          => $this->faker->randomElement(['France', 'États-Unis', 'Royaume-Uni', 'Allemagne', 'Italie']),
            FilmSchema::LANGUE_ORIGINALE      => $this->faker->randomElement(['français', 'anglais', 'espagnol', 'allemand', 'italien']),
            FilmSchema::SOUS_TITRES           => $this->faker->optional(0.8)->randomElements($sousTitresDisponibles, rand(1, 3)),
            FilmSchema::REALISATEURS          => [$this->faker->name(), $this->faker->optional(0.3)->name()], // Array comme Domain
            FilmSchema::ACTEURS_PRINCIPAUX    => [
                $this->faker->name(),
                $this->faker->name(),
                $this->faker->optional(0.8)->name(),
            ],
            FilmSchema::PRODUCTEUR        => $this->faker->optional(0.8)->company(),
            FilmSchema::AFFICHE_URL       => $this->faker->optional(0.9)->imageUrl(300, 450, 'movies'),
            FilmSchema::BANDE_ANNONCE_URL => $this->faker->optional(0.7)->url(),
            FilmSchema::NOTE_CRITIQUE     => $this->faker->optional(0.7)->randomFloat(1, 0, 10),
            FilmSchema::NOTE_PUBLIC       => $this->faker->optional(0.8)->randomFloat(1, 0, 10),
            FilmSchema::NOTE_MOYENNE_AVIS => $this->faker->optional(0.6)->randomFloat(1, 0, 10),
            FilmSchema::NOMBRE_AVIS       => $this->faker->numberBetween(0, 500),
            FilmSchema::STATUT            => $this->faker->randomElement($statuts),
            FilmSchema::EST_ACTIF         => $this->faker->boolean(85), // 85% des films sont actifs
        ];
    }

    /**
     * Film français récent
     */
    public function francais(): static
    {
        return $this->state(fn (array $attributes) => [
            FilmSchema::GENRES           => $this->faker->randomElements(['Comédie', 'Drame', 'Romance'], rand(1, 2)),
            FilmSchema::DATE_SORTIE      => $this->faker->dateTimeBetween('-1 year', '+3 months'),
            FilmSchema::PAYS_ORIGINE     => 'France',
            FilmSchema::LANGUE_ORIGINALE => 'français',
            FilmSchema::SOUS_TITRES      => ['anglais'],
        ]);
    }

    /**
     * Film blockbuster américain
     */
    public function blockbuster(): static
    {
        return $this->state(fn (array $attributes) => [
            FilmSchema::GENRES           => $this->faker->randomElements(['Action', 'Aventure', 'Science-Fiction'], rand(1, 2)),
            FilmSchema::DUREE_MINUTES    => $this->faker->numberBetween(120, 180),
            FilmSchema::NOTE_CRITIQUE    => $this->faker->randomFloat(1, 3, 10),
            FilmSchema::NOTE_PUBLIC      => $this->faker->randomFloat(1, 3.5, 10),
            FilmSchema::NOMBRE_AVIS      => $this->faker->numberBetween(100, 2000),
            FilmSchema::STATUT           => 'EN_SALLE',
            FilmSchema::PAYS_ORIGINE     => 'États-Unis',
            FilmSchema::LANGUE_ORIGINALE => 'anglais',
            FilmSchema::SOUS_TITRES      => ['français', 'espagnol'],
        ]);
    }

    /**
     * Film d'art et d'essai
     */
    public function artEtEssai(): static
    {
        return $this->state(fn (array $attributes) => [
            FilmSchema::GENRES        => $this->faker->randomElements(['Drame', 'Documentaire', 'Historique'], rand(1, 2)),
            FilmSchema::DUREE_MINUTES => $this->faker->numberBetween(90, 150),
            FilmSchema::NOTE_CRITIQUE => $this->faker->randomFloat(1, 3.5, 10),
            FilmSchema::NOMBRE_AVIS   => $this->faker->numberBetween(10, 200),
            FilmSchema::STATUT        => 'EN_SALLE',
        ]);
    }
}
