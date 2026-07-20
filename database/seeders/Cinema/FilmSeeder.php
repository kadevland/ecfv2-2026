<?php

declare(strict_types=1);

namespace Database\Seeders\Cinema;

use DateTime;
use Illuminate\Database\Seeder;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;

final class FilmSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRealFilms();
        $this->createFakeFilms();
    }

    private function createRealFilms(): void
    {
        // Films français populaires - SCHEMA CONSTANTS + NOUVEAUX CHAMPS DOMAIN
        Film::create([
            FilmSchema::ID                    => FilmId::generate(),
            FilmSchema::TITRE                 => 'Astérix & Obélix : L\'Empire du Milieu',
            FilmSchema::TITRE_ORIGINAL        => 'Astérix & Obélix : L\'Empire du Milieu',
            FilmSchema::SYNOPSIS              => 'Nos héros gaulois partent en Chine pour aider la princesse Fu Yi.',
            FilmSchema::GENRES                => ['Comédie', 'Aventure'], // ARRAY comme Domain
            FilmSchema::DUREE_MINUTES         => 112,
            FilmSchema::CLASSIFICATION        => 'TOUS_PUBLICS',
            FilmSchema::DATE_SORTIE           => new DateTime('2023-02-01'),
            FilmSchema::DATE_FIN_EXPLOITATION => new DateTime('2024-06-01'),
            FilmSchema::PAYS_ORIGINE          => 'France',
            FilmSchema::LANGUE_ORIGINALE      => 'français',
            FilmSchema::SOUS_TITRES           => ['anglais', 'espagnol'],
            FilmSchema::REALISATEURS          => ['Guillaume Canet'], // ARRAY comme Domain
            FilmSchema::ACTEURS_PRINCIPAUX    => ['Guillaume Canet', 'Gilles Lellouche', 'Vincent Cassel'],
            FilmSchema::PRODUCTEUR            => 'Les Films du Trésor',
            FilmSchema::AFFICHE_URL           => 'https://example.com/asterix-poster.jpg',
            FilmSchema::BANDE_ANNONCE_URL     => 'https://example.com/asterix-trailer.mp4',
            FilmSchema::NOTE_CRITIQUE         => 3.2,
            FilmSchema::NOTE_PUBLIC           => 3.8,
            FilmSchema::NOTE_MOYENNE_AVIS     => 3.5,
            FilmSchema::NOMBRE_AVIS           => 247,
            FilmSchema::STATUT                => 'EN_SALLE',
            FilmSchema::EST_ACTIF             => true,
        ]);

        Film::create([
            FilmSchema::ID                    => FilmId::generate(),
            FilmSchema::TITRE                 => 'Avatar: The Way of Water',
            FilmSchema::TITRE_ORIGINAL        => 'Avatar: The Way of Water',
            FilmSchema::SYNOPSIS              => 'Jake Sully et sa famille explorent les océans de Pandora.',
            FilmSchema::GENRES                => ['Science-Fiction', 'Aventure'],
            FilmSchema::DUREE_MINUTES         => 192,
            FilmSchema::CLASSIFICATION        => 'MOINS_12',
            FilmSchema::DATE_SORTIE           => new DateTime('2022-12-14'),
            FilmSchema::DATE_FIN_EXPLOITATION => new DateTime('2024-03-14'),
            FilmSchema::PAYS_ORIGINE          => 'États-Unis',
            FilmSchema::LANGUE_ORIGINALE      => 'anglais',
            FilmSchema::SOUS_TITRES           => ['français', 'espagnol', 'allemand'],
            FilmSchema::REALISATEURS          => ['James Cameron'],
            FilmSchema::ACTEURS_PRINCIPAUX    => ['Sam Worthington', 'Zoe Saldana', 'Kate Winslet'],
            FilmSchema::PRODUCTEUR            => '20th Century Studios',
            FilmSchema::AFFICHE_URL           => 'https://example.com/avatar2-poster.jpg',
            FilmSchema::BANDE_ANNONCE_URL     => 'https://example.com/avatar2-trailer.mp4',
            FilmSchema::NOTE_CRITIQUE         => 4.1,
            FilmSchema::NOTE_PUBLIC           => 4.3,
            FilmSchema::NOTE_MOYENNE_AVIS     => 4.2,
            FilmSchema::NOMBRE_AVIS           => 1847,
            FilmSchema::STATUT                => 'EN_SALLE',
            FilmSchema::EST_ACTIF             => true,
        ]);

        Film::create([
            FilmSchema::ID                    => FilmId::generate(),
            FilmSchema::TITRE                 => 'Oppenheimer',
            FilmSchema::TITRE_ORIGINAL        => 'Oppenheimer',
            FilmSchema::SYNOPSIS              => 'L\'histoire du physicien J. Robert Oppenheimer et la création de la bombe atomique.',
            FilmSchema::GENRES                => ['Drame', 'Historique', 'Biographie'],
            FilmSchema::DUREE_MINUTES         => 180,
            FilmSchema::CLASSIFICATION        => 'MOINS_16',
            FilmSchema::DATE_SORTIE           => new DateTime('2023-07-19'),
            FilmSchema::DATE_FIN_EXPLOITATION => new DateTime('2024-01-19'),
            FilmSchema::PAYS_ORIGINE          => 'États-Unis',
            FilmSchema::LANGUE_ORIGINALE      => 'anglais',
            FilmSchema::SOUS_TITRES           => ['français', 'espagnol'],
            FilmSchema::REALISATEURS          => ['Christopher Nolan'],
            FilmSchema::ACTEURS_PRINCIPAUX    => ['Cillian Murphy', 'Emily Blunt', 'Robert Downey Jr.'],
            FilmSchema::PRODUCTEUR            => 'Universal Pictures',
            FilmSchema::AFFICHE_URL           => 'https://example.com/oppenheimer-poster.jpg',
            FilmSchema::BANDE_ANNONCE_URL     => 'https://example.com/oppenheimer-trailer.mp4',
            FilmSchema::NOTE_CRITIQUE         => 4.6,
            FilmSchema::NOTE_PUBLIC           => 4.4,
            FilmSchema::NOTE_MOYENNE_AVIS     => 4.5,
            FilmSchema::NOMBRE_AVIS           => 923,
            FilmSchema::STATUT                => 'EN_SALLE',
            FilmSchema::EST_ACTIF             => true,
        ]);

        // Film français d'art et d'essai
        Film::create([
            FilmSchema::ID                    => FilmId::generate(),
            FilmSchema::TITRE                 => 'Anatomie d\'une chute',
            FilmSchema::TITRE_ORIGINAL        => 'Anatomie d\'une chute',
            FilmSchema::SYNOPSIS              => 'Une femme est soupçonnée de meurtre après la mort de son mari.',
            FilmSchema::GENRES                => ['Drame', 'Policier'],
            FilmSchema::DUREE_MINUTES         => 151,
            FilmSchema::CLASSIFICATION        => 'MOINS_16',
            FilmSchema::DATE_SORTIE           => new DateTime('2023-08-23'),
            FilmSchema::DATE_FIN_EXPLOITATION => new DateTime('2024-02-23'),
            FilmSchema::PAYS_ORIGINE          => 'France',
            FilmSchema::LANGUE_ORIGINALE      => 'français',
            FilmSchema::SOUS_TITRES           => ['anglais'],
            FilmSchema::REALISATEURS          => ['Justine Triet'],
            FilmSchema::ACTEURS_PRINCIPAUX    => ['Sandra Hüller', 'Swann Arlaud', 'Milo Machado-Graner'],
            FilmSchema::PRODUCTEUR            => 'Les Films Pelléas',
            FilmSchema::AFFICHE_URL           => 'https://example.com/anatomie-poster.jpg',
            FilmSchema::BANDE_ANNONCE_URL     => 'https://example.com/anatomie-trailer.mp4',
            FilmSchema::NOTE_CRITIQUE         => 4.3,
            FilmSchema::NOTE_PUBLIC           => 4.1,
            FilmSchema::NOTE_MOYENNE_AVIS     => 4.2,
            FilmSchema::NOMBRE_AVIS           => 412,
            FilmSchema::STATUT                => 'EN_SALLE',
            FilmSchema::EST_ACTIF             => true,
        ]);

        // Film inactif pour tests
        Film::create([
            FilmSchema::ID                    => FilmId::generate(),
            FilmSchema::TITRE                 => 'Film Retiré de l\'Affiche',
            FilmSchema::TITRE_ORIGINAL        => 'Film Retiré de l\'Affiche',
            FilmSchema::SYNOPSIS              => 'Un film qui n\'est plus à l\'affiche.',
            FilmSchema::GENRES                => ['Drame'],
            FilmSchema::DUREE_MINUTES         => 95,
            FilmSchema::CLASSIFICATION        => 'TOUS_PUBLICS',
            FilmSchema::DATE_SORTIE           => new DateTime('2020-03-15'),
            FilmSchema::DATE_FIN_EXPLOITATION => new DateTime('2020-08-15'),
            FilmSchema::PAYS_ORIGINE          => 'France',
            FilmSchema::LANGUE_ORIGINALE      => 'français',
            FilmSchema::SOUS_TITRES           => null,
            FilmSchema::REALISATEURS          => ['Réalisateur Inconnu'],
            FilmSchema::ACTEURS_PRINCIPAUX    => ['Acteur A', 'Actrice B'],
            FilmSchema::PRODUCTEUR            => null,
            FilmSchema::AFFICHE_URL           => null,
            FilmSchema::BANDE_ANNONCE_URL     => null,
            FilmSchema::NOTE_CRITIQUE         => 2.5,
            FilmSchema::NOTE_PUBLIC           => 2.8,
            FilmSchema::NOTE_MOYENNE_AVIS     => 2.7,
            FilmSchema::NOMBRE_AVIS           => 34,
            FilmSchema::STATUT                => 'ARCHIVE',
            FilmSchema::EST_ACTIF             => false,
        ]);
    }

    private function createFakeFilms(): void
    {
        // Films français générés
        Film::factory()->francais()->count(8)->create();

        // Blockbusters américains
        Film::factory()->blockbuster()->count(6)->create();

        // Films d'art et d'essai
        Film::factory()->artEtEssai()->count(4)->create();

        // Films divers
        Film::factory()->count(12)->create();
    }
}
