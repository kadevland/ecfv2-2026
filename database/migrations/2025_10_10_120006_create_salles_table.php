<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

return new class extends Migration
{
    protected $connection = SalleSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma cinema s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(SalleSchema::CONNECTION));
        $helper->createSchemaIfNotExists(SalleSchema::SCHEMA);

        Schema::create(SalleSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(SalleSchema::PRIMARY_KEY)->primary();

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(SalleSchema::ID)
                ->unique();

            // Référence vers le cinéma (dual FK)
            $table->bigInteger(SalleSchema::CINEMA_KEY)->unsigned(); // FK technique (performance)
            $table->uuid(SalleSchema::CINEMA_ID);                    // FK métier (business logic)

            // Informations de base
            $table->string(SalleSchema::NOM, 100)
                ->comment('Nom/numéro de la salle');

            $table->integer(SalleSchema::CAPACITE_TOTALE)
                ->comment('Nombre total de places');

            $table->integer(SalleSchema::NOMBRE_RANGEES)
                ->comment('Nombre de rangées');

            $table->integer(SalleSchema::PLACES_PAR_RANGEE)
                ->comment('Nombre moyen de places par rangée');

            // Configuration des places (seulement standard et PMR)
            $table->integer(SalleSchema::PLACES_STANDARD)
                ->default(0)
                ->comment('Places standard');

            $table->integer(SalleSchema::PLACES_PMR)
                ->default(0)
                ->comment('Places pour personnes à mobilité réduite');

            // Qualités techniques en JSON (les 2 seuls champs JSON pour qualités)
            $table->jsonb(SalleSchema::QUALITE_PROJECTION)
                ->nullable()
                ->comment('Qualités de projection disponibles (2K, 4K, IMAX, etc.)');

            $table->jsonb(SalleSchema::QUALITE_SONORE)
                ->nullable()
                ->comment('Qualités sonores disponibles (Dolby Atmos, DTS, etc.)');

            // Équipements et configuration
            $table->boolean(SalleSchema::CLIMATISATION)
                ->default(true)
                ->comment('Climatisation disponible');

            $table->boolean(SalleSchema::ACCESSIBILITE_PMR)
                ->default(true)
                ->comment('Accessibilité PMR');

            $table->jsonb(SalleSchema::PLAN_SALLE)
                ->nullable()
                ->comment('Configuration détaillée des sièges');

            // Statut et gestion
            $table->enum(SalleSchema::STATUT, [
                'ACTIVE',
                'MAINTENANCE',
                'RENOVATION',
                'HORS_SERVICE',
            ])->default('ACTIVE')
                ->comment('Statut de la salle');

            // Informations techniques
            // $table->decimal(SalleSchema::SUPERFICIE_M2, 8, 2)
            //     ->nullable()
            //     ->comment('Superficie en m²');

            // $table->decimal(SalleSchema::HAUTEUR_PLAFOND, 5, 2)
            //     ->nullable()
            //     ->comment('Hauteur sous plafond en mètres');

            // $table->text(SalleSchema::NOTES_TECHNIQUES)
            //     ->nullable()
            //     ->comment('Notes techniques diverses');

            $table->timestampsTz();

            // FK vers cinema (dual contraintes)
            $table->foreign(SalleSchema::CINEMA_KEY)
                ->references(CinemaSchema::PRIMARY_KEY)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(SalleSchema::CINEMA_ID)
                ->references(CinemaSchema::ID)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour recherche UUID business
            $table->index(SalleSchema::ID, SalleSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(SalleSchema::CINEMA_KEY, SalleSchema::INDEX_CINEMA_DB);
            $table->index(SalleSchema::CINEMA_ID, SalleSchema::INDEX_CINEMA);
            $table->index(SalleSchema::NOM, SalleSchema::INDEX_NOM);
            $table->index(SalleSchema::CAPACITE_TOTALE, SalleSchema::INDEX_CAPACITE);
            $table->index(SalleSchema::STATUT, SalleSchema::INDEX_STATUT);
            // Note: Index GIN sur JSONB créés séparément avec addGinIndex()

            // Index composé pour recherche avancée (utilise FK technique pour performance)
            $table->index([SalleSchema::CINEMA_KEY, SalleSchema::STATUT], SalleSchema::INDEX_CINEMA_STATUT);
            $table->index([SalleSchema::CAPACITE_TOTALE, SalleSchema::STATUT], SalleSchema::INDEX_CAPACITE_STATUT);

            // Contrainte unique nom salle par cinéma (utilise FK technique)
            $table->unique([SalleSchema::CINEMA_KEY, SalleSchema::NOM], SalleSchema::UNIQUE_CINEMA_NOM);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
        });

        // Index GIN sur JSONB pour plan salle
        $helper->addGinIndex(
            SalleSchema::FULL_TABLE,
            SalleSchema::PLAN_SALLE,
            SalleSchema::INDEX_PLAN_SALLE
        );

        // Index GIN sur les nouveaux champs JSON qualités
        $helper->addGinIndex(
            SalleSchema::FULL_TABLE,
            SalleSchema::QUALITE_PROJECTION,
            SalleSchema::INDEX_QUALITE_PROJECTION
        );

        $helper->addGinIndex(
            SalleSchema::FULL_TABLE,
            SalleSchema::QUALITE_SONORE,
            SalleSchema::INDEX_QUALITE_SONORE
        );

        // Commentaire sur la table
        $helper->addTableComment(
            SalleSchema::FULL_TABLE,
            'Salles de cinéma avec configuration détaillée et équipements'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SalleSchema::FULL_TABLE);
    }
};
