<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;

return new class extends Migration
{
    protected $connection = SeanceSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma cinema s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(SeanceSchema::CONNECTION));
        $helper->createSchemaIfNotExists(SeanceSchema::SCHEMA);

        // Activer l'extension btree_gist nécessaire pour les contraintes d'exclusion
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

        Schema::create(SeanceSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(SeanceSchema::PRIMARY_KEY)->primary();

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(SeanceSchema::ID)
                ->unique();

            // Références vers film et salle (dual FK)
            $table->bigInteger(SeanceSchema::FILM_KEY)->unsigned();
            $table->uuid(SeanceSchema::FILM_ID);
            $table->bigInteger(SeanceSchema::SALLE_KEY)->unsigned();
            $table->uuid(SeanceSchema::SALLE_ID);

            // Informations temporelles
            $table->date(SeanceSchema::DATE_SEANCE);
            $table->time(SeanceSchema::HEURE_DEBUT);
            $table->time(SeanceSchema::HEURE_FIN);

            // Version et langue
            $table->enum(SeanceSchema::VERSION, [
                'VF',
                'VOST',
                'VO',
                'VF_AUDIODESCRIPTION',
                'VOST_AUDIODESCRIPTION',
            ])->default('VF');

            $table->boolean(SeanceSchema::EST_3D)
                ->default(false);

            // Tarification MoneyPHP - Prix unique par séance
            $table->integer(SeanceSchema::PRIX_HT_CENTIMES)
                ->comment('Prix HT en centimes pour MoneyPHP');

            $table->string(SeanceSchema::DEVISE, 3)
                ->default('EUR')
                ->comment('Code devise ISO');

            $table->integer(SeanceSchema::TAUX_TVA_BASIS_POINTS)
                ->comment('Taux TVA en basis points (2000 = 20%)');

            $table->integer(SeanceSchema::PRIX_TTC_CENTIMES)
                ->comment('Prix TTC calculé en centimes');

            // Informations de réservation
            $table->integer(SeanceSchema::PLACES_DISPONIBLES)
                ->comment('Places disponibles (calculé)');

            $table->integer(SeanceSchema::PLACES_RESERVEES)
                ->default(0)
                ->comment('Places déjà réservées');

            $table->integer(SeanceSchema::PLACES_VENDUES)
                ->default(0)
                ->comment('Places vendues (confirmées)');

            // Statut et gestion
            $table->enum(SeanceSchema::STATUT, [
                'PROGRAMMEE',
                'VENTE_OUVERTE',
                'COMPLET',
                'EN_COURS',
                'TERMINEE',
                'ANNULEE',
            ])->default('PROGRAMMEE');

            $table->timestampTz(SeanceSchema::OUVERTURE_VENTE)
                ->nullable()
                ->comment('Date d\'ouverture de la vente');

            $table->timestampTz(SeanceSchema::FERMETURE_VENTE)
                ->nullable()
                ->comment('Date de fermeture de la vente');

            // Configuration spéciale
            $table->boolean(SeanceSchema::SEANCE_SPECIALE)
                ->default(false)
                ->comment('Avant-première, séance de gala, etc.');

            $table->string(SeanceSchema::TYPE_SEANCE, 100)
                ->nullable()
                ->comment('Avant-première, festival, etc.');

            $table->text(SeanceSchema::NOTES)
                ->nullable();

            // Combined datetime columns expected by domain model
            $table->timestamp(SeanceSchema::DATE_HEURE_DEBUT)->nullable();
            $table->timestamp(SeanceSchema::DATE_HEURE_FIN)->nullable();

            // Complex value object columns expected by domain model
            $table->jsonb(SeanceSchema::TARIFICATION)->nullable()->comment('Tarification complex object as JSON');
            $table->jsonb(SeanceSchema::TAUX_TVA)->nullable()->comment('TauxTva value object as JSON');
            $table->boolean(SeanceSchema::PLACEMENT_LIBRE)->default(false);

            // Colonnes directes pour qualités et durée additionnelle
            $table->integer(SeanceSchema::DUREE_ADDITIONNELLE)->nullable()->comment('Durée additionnelle en minutes');
            $table->string(SeanceSchema::QUALITE_PROJECTION, 50)->nullable()->comment('Qualité de projection');
            $table->string(SeanceSchema::QUALITE_SONORE, 50)->nullable()->comment('Qualité sonore');

            // Métadonnées techniques
            $table->jsonb(SeanceSchema::CONFIGURATION_TECHNIQUE)
                ->nullable()
                ->comment('Config audio/vidéo spécifique');

            $table->jsonb(SeanceSchema::METADONNEES_COMMERCIALES)
                ->nullable()
                ->comment('Promotions, partenariats, etc.');

            $table->timestampsTz();

            // FK vers film
            $table->foreign(SeanceSchema::FILM_KEY)
                ->references(FilmSchema::PRIMARY_KEY)
                ->on(FilmSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(SeanceSchema::FILM_ID)
                ->references(FilmSchema::ID)
                ->on(FilmSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK vers salle
            $table->foreign(SeanceSchema::SALLE_KEY)
                ->references(SalleSchema::PRIMARY_KEY)
                ->on(SalleSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(SeanceSchema::SALLE_ID)
                ->references(SalleSchema::ID)
                ->on(SalleSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour recherche UUID business
            $table->index(SeanceSchema::ID, SeanceSchema::INDEX_UUID);

            // Indexes pour performance et recherche
            $table->index(SeanceSchema::FILM_KEY, SeanceSchema::INDEX_FILM);
            $table->index(SeanceSchema::SALLE_KEY, SeanceSchema::INDEX_SALLE);
            $table->index(SeanceSchema::DATE_SEANCE, SeanceSchema::INDEX_DATE);
            $table->index(SeanceSchema::HEURE_DEBUT, SeanceSchema::INDEX_HEURE);
            $table->index(SeanceSchema::STATUT, SeanceSchema::INDEX_STATUT);
            $table->index(SeanceSchema::VERSION, SeanceSchema::INDEX_VERSION);
            $table->index(SeanceSchema::EST_3D, SeanceSchema::INDEX_3D);
            $table->index(SeanceSchema::QUALITE_PROJECTION, SeanceSchema::INDEX_QUALITE_PROJECTION);
            $table->index(SeanceSchema::QUALITE_SONORE, SeanceSchema::INDEX_QUALITE_SONORE);
            $table->index(SeanceSchema::DUREE_ADDITIONNELLE, SeanceSchema::INDEX_DUREE_ADDITIONNELLE);

            // Index composés pour recherche avancée
            $table->index([SeanceSchema::DATE_SEANCE, SeanceSchema::STATUT], SeanceSchema::INDEX_DATE_STATUT);
            $table->index([SeanceSchema::FILM_KEY, SeanceSchema::DATE_SEANCE], SeanceSchema::INDEX_FILM_DATE);
            $table->index([SeanceSchema::SALLE_KEY, SeanceSchema::DATE_SEANCE], SeanceSchema::INDEX_SALLE_DATE);
            $table->index([SeanceSchema::DATE_SEANCE, SeanceSchema::HEURE_DEBUT], SeanceSchema::INDEX_PROGRAMMATION);

            // Contrainte unique : pas de conflit horaire dans une salle
            $table->unique([
                SeanceSchema::SALLE_KEY,
                SeanceSchema::DATE_SEANCE,
                SeanceSchema::HEURE_DEBUT,
            ], SeanceSchema::UNIQUE_SALLE_HORAIRE);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries configuration et métadonnées
        $helper->addGinIndex(
            SeanceSchema::FULL_TABLE,
            SeanceSchema::CONFIGURATION_TECHNIQUE,
            SeanceSchema::INDEX_CONFIG_TECHNIQUE
        );

        $helper->addGinIndex(
            SeanceSchema::FULL_TABLE,
            SeanceSchema::METADONNEES_COMMERCIALES,
            SeanceSchema::INDEX_METADONNEES_COMMERCIALES
        );

        // Contrainte d'exclusion pour éviter les chevauchements de séances dans une même salle
        // Utilise les types de données PostgreSQL tsrange pour les intervalles temporels
        //
        // Fonctionnement:
        // - salle_db_id WITH = : même salle
        // - tsrange(debut, fin) WITH && : intervalles temporels qui se chevauchent
        // - WHERE statut NOT IN ('ANNULEE', 'TERMINEE') : exclut les séances terminées/annulées
        //
        // Exemple d'erreur automatique:
        // Séance 1: 14h00-16h00 ✅ OK
        // Séance 2: 15h00-17h00 ❌ CONFLICT - chevauche avec séance 1
        // Séance 3: 16h30-18h30 ✅ OK - pas de chevauchement
        DB::statement('
            ALTER TABLE ' . SeanceSchema::FULL_TABLE . '
            ADD CONSTRAINT ' . SeanceSchema::CONSTRAINT_NO_OVERLAP . '
            EXCLUDE USING gist (
                ' . SeanceSchema::SALLE_KEY . ' WITH =,
                tsrange(' . SeanceSchema::DATE_HEURE_DEBUT . ', ' . SeanceSchema::DATE_HEURE_FIN . ') WITH &&
            )
            WHERE (' . SeanceSchema::STATUT . " NOT IN ('ANNULEE', 'TERMINEE'))
        ");

        // Commentaire sur la table
        $helper->addTableComment(
            SeanceSchema::FULL_TABLE,
            'Séances de cinéma avec tarification MoneyPHP et gestion des places'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraint before dropping table
        DB::statement('ALTER TABLE ' . SeanceSchema::FULL_TABLE . ' DROP CONSTRAINT IF EXISTS ' . SeanceSchema::CONSTRAINT_NO_OVERLAP);

        Schema::dropIfExists(SeanceSchema::FULL_TABLE);
    }
};
