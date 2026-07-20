<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

return new class extends Migration
{
    protected $connection = CinemaSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create(CinemaSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(CinemaSchema::PRIMARY_KEY)->primary();

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(CinemaSchema::ID)
                ->unique();

            // Business fields
            $table->string(CinemaSchema::NOM);
            $table->string(CinemaSchema::PAYS, 100);
            $table->jsonb(CinemaSchema::ADRESSE); // JSONB pour performance + index GIN
            $table->string(CinemaSchema::TELEPHONE, 20)
                ->nullable();
            $table->string(CinemaSchema::EMAIL, 320)
                ->nullable();
            $table->boolean(CinemaSchema::EST_ACTIF)
                ->default(true);
            $table->text(CinemaSchema::DESCRIPTION)
                ->nullable();
            $table->jsonb(CinemaSchema::COORDONNEES_GPS)
                ->nullable(); // JSONB pour coordonnées GPS {latitude, longitude}
            $table->jsonb(CinemaSchema::HORAIRES_OUVERTURE)->nullable();

            // Metadata
            $table->timestamps();

            // Index pour recherche UUID business
            $table->index(CinemaSchema::ID, CinemaSchema::INDEX_UUID);

            // Index pour recherche pays
            $table->index(CinemaSchema::PAYS, CinemaSchema::INDEX_PAYS);

            // Contrainte unique business (nom + pays pour éviter doublons)
            $table->unique([CinemaSchema::NOM, CinemaSchema::PAYS], CinemaSchema::UNIQUE_NOM_PAYS);
        });

        // Index GIN sur JSONB pour queries adresse et GPS (après création table)
        $helper = new SchemaHelper(DB::connection(CinemaSchema::CONNECTION));
        $helper->addGinIndex(
            CinemaSchema::FULL_TABLE,
            CinemaSchema::ADRESSE,
            CinemaSchema::INDEX_ADRESSE
        );

        // Index GIN sur JSONB pour queries géospatiales GPS
        $helper->addGinIndex(
            CinemaSchema::FULL_TABLE,
            CinemaSchema::COORDONNEES_GPS,
            CinemaSchema::INDEX_COORDONNEES_GPS
        );

        // Commentaire sur la table
        /*$helper->addTableComment(
            CinemaSchema::FULL_TABLE,
            'Table des cinémas avec informations de base, adresse et coordonnées GPS en JSONB'
        );*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(CinemaSchema::FULL_TABLE);
    }
};
