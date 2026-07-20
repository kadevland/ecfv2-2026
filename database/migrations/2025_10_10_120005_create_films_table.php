<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;

return new class extends Migration
{
    protected $connection = FilmSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma cinema s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(FilmSchema::CONNECTION));
        $helper->createSchemaIfNotExists(FilmSchema::SCHEMA);

        Schema::create(FilmSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(FilmSchema::PRIMARY_KEY)->primary();

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(FilmSchema::ID)
                ->unique();

            // Informations principales du film
            $table->string(FilmSchema::TITRE, 300);
            $table->string(FilmSchema::TITRE_ORIGINAL, 300)
                ->nullable();
            $table->text(FilmSchema::SYNOPSIS)
                ->nullable();

            // Métadonnées cinématographiques - ARRAY comme Domain Entity
            $table->jsonb(FilmSchema::GENRES)
                ->comment('Array des genres du film');

            $table->integer(FilmSchema::DUREE_MINUTES)
                ->comment('Durée en minutes');

            $table->date(FilmSchema::DATE_SORTIE);

            // Date fin exploitation - Domain Entity property
            $table->timestampTz(FilmSchema::DATE_FIN_EXPLOITATION)
                ->nullable()
                ->comment('Date de fin d\'exploitation en salle');

            $table->string(FilmSchema::PAYS_ORIGINE, 100);

            $table->string(FilmSchema::LANGUE_ORIGINALE, 50)
                ->default('français');

            // Sous-titres disponibles - Domain Entity property
            $table->jsonb(FilmSchema::SOUS_TITRES)
                ->nullable()
                ->comment('Array des langues de sous-titres disponibles');

            // Classification
            $table->enum(FilmSchema::CLASSIFICATION, \App\Domain\Cinema\Enums\ClassificationFilmEnum::values())
                ->default(\App\Domain\Cinema\Enums\ClassificationFilmEnum::TOUS_PUBLICS->value);

            // Équipe technique - ARRAY comme Domain Entity
            $table->jsonb(FilmSchema::REALISATEURS)
                ->comment('Array des réalisateurs du film');
            $table->text(FilmSchema::ACTEURS_PRINCIPAUX)
                ->nullable()
                ->comment('JSON array des acteurs principaux');
            $table->string(FilmSchema::PRODUCTEUR, 200)
                ->nullable();

            // Médias et promotion
            $table->string(FilmSchema::AFFICHE_URL, 500)
                ->nullable();
            $table->string(FilmSchema::BANDE_ANNONCE_URL, 500)
                ->nullable();
            $table->jsonb(FilmSchema::IMAGES_ADDITIONNELLES)
                ->nullable()
                ->comment('URLs des images additionnelles');

            // Données commerciales
            $table->decimal(FilmSchema::NOTE_CRITIQUE, 3, 1)
                ->nullable()
                ->comment('Note critique sur 10');
            $table->decimal(FilmSchema::NOTE_PUBLIC, 3, 1)
                ->nullable()
                ->comment('Note public sur 10');

            // Avis utilisateurs - Domain Entity properties calculées
            $table->decimal(FilmSchema::NOTE_MOYENNE_AVIS, 3, 1)
                ->nullable()
                ->comment('Note moyenne calculée des avis utilisateurs sur 10');

            $table->integer(FilmSchema::NOMBRE_AVIS)
                ->default(0)
                ->comment('Nombre total d\'avis utilisateurs');

            // Statut et gestion
            $table->enum(FilmSchema::STATUT, [
                'A_VENIR',
                'EN_SALLE',
                'ARCHIVE',
                'SUSPENDU',
            ])->default('A_VENIR');

            $table->boolean(FilmSchema::EST_ACTIF)
                ->default(true);

            // Métadonnées techniques
            $table->jsonb(FilmSchema::METADONNEES_TECHNIQUES)
                ->nullable()
                ->comment('Format, codec, résolution, etc.');

            $table->timestampsTz();

            // Index pour recherche UUID business
            $table->index(FilmSchema::ID, FilmSchema::INDEX_UUID);

            // Indexes pour recherche et filtrage
            $table->index(FilmSchema::TITRE, FilmSchema::INDEX_TITRE);
            $table->index(FilmSchema::DATE_SORTIE, FilmSchema::INDEX_DATE_SORTIE);
            $table->index(FilmSchema::DATE_FIN_EXPLOITATION, FilmSchema::INDEX_DATE_FIN_EXPLOITATION);
            $table->index(FilmSchema::CLASSIFICATION, FilmSchema::INDEX_CLASSIFICATION);
            $table->index(FilmSchema::STATUT, FilmSchema::INDEX_STATUT);
            $table->index(FilmSchema::NOTE_MOYENNE_AVIS, FilmSchema::INDEX_NOTE_MOYENNE);

            // Index composé pour recherche avancée
            $table->index([FilmSchema::DATE_SORTIE, FilmSchema::STATUT], FilmSchema::INDEX_DATE_STATUT);

            // Contrainte unique sur titre + date sortie pour éviter doublons
            $table->unique([FilmSchema::TITRE, FilmSchema::DATE_SORTIE], FilmSchema::UNIQUE_TITRE_DATE);
        });

        // Index GIN sur JSONB pour queries optimisées (Domain Entity arrays)
        $helper->addGinIndex(
            FilmSchema::FULL_TABLE,
            FilmSchema::REALISATEURS,
            FilmSchema::INDEX_REALISATEURS
        );

        $helper->addGinIndex(
            FilmSchema::FULL_TABLE,
            FilmSchema::GENRES,
            FilmSchema::INDEX_GENRES
        );

        $helper->addGinIndex(
            FilmSchema::FULL_TABLE,
            FilmSchema::IMAGES_ADDITIONNELLES,
            FilmSchema::INDEX_IMAGES
        );

        $helper->addGinIndex(
            FilmSchema::FULL_TABLE,
            FilmSchema::METADONNEES_TECHNIQUES,
            FilmSchema::INDEX_METADONNEES
        );

        // Commentaire sur la table
        $helper->addTableComment(
            FilmSchema::FULL_TABLE,
            'Table des films avec métadonnées complètes et support JSONB'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(FilmSchema::FULL_TABLE);
    }
};
