<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;
use App\Infrastructure\Database\Schemas\Employees\IncidentSchema;

return new class extends Migration
{
    protected $connection = IncidentSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma employees s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(IncidentSchema::CONNECTION));
        $helper->createSchemaIfNotExists(IncidentSchema::SCHEMA);

        Schema::create(IncidentSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(IncidentSchema::PRIMARY_KEY)->primary();

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(IncidentSchema::ID)
                ->unique();

            // Numéro d'incident unique pour US15
            $table->string(IncidentSchema::NUMERO_INCIDENT, 20)
                ->unique()
                ->comment('Numéro unique d\'incident (ex: INC-2025-001234)');

            // Références vers entités concernées
            $table->id(IncidentSchema::CONTRAT_RAPPORTEUR_ID)
                ->comment('Employé qui rapporte l\'incident');

            $table->id(IncidentSchema::CINEMA_DB_ID);
            $table->uuid(IncidentSchema::CINEMA_UUID);

            $table->unsignedBigInteger(IncidentSchema::SALLE_DB_ID)
                ->nullable()
                ->comment('Salle concernée si applicable');

            $table->unsignedBigInteger(IncidentSchema::SEANCE_DB_ID)
                ->nullable()
                ->comment('Séance concernée si applicable');

            // Classification de l'incident
            $table->enum(IncidentSchema::TYPE_INCIDENT, [
                'TECHNIQUE',
                'SECURITE',
                'CLIENTELE',
                'PERSONNEL',
                'MATERIEL',
                'HYGIENE',
                'INFORMATIQUE',
                'AUTRE',
            ]);

            $table->enum(IncidentSchema::CATEGORIE, [
                'PANNE_PROJECTION',
                'PANNE_AUDIO',
                'PROBLEME_CLIMATISATION',
                'PROBLEME_ELECTRICITE',
                'PROBLEME_ACCES',
                'ACCIDENT_CLIENT',
                'ACCIDENT_PERSONNEL',
                'VOL_DEGRADATION',
                'COMPORTEMENT_INAPPROPRIE',
                'EVACUATION',
                'INCENDIE',
                'MEDICAL',
                'CONFLIT_PERSONNEL',
                'ERREUR_CAISSE',
                'AUTRE',
            ]);

            $table->enum(IncidentSchema::NIVEAU_GRAVITE, [
                'MINEUR',
                'MODERE',
                'MAJEUR',
                'CRITIQUE',
                'URGENCE',
            ])->default('MINEUR');

            $table->enum(IncidentSchema::NIVEAU_PRIORITE, [
                'BASSE',
                'NORMALE',
                'HAUTE',
                'URGENTE',
                'CRITIQUE',
            ])->default('NORMALE');

            // Informations temporelles
            $table->timestampTz(IncidentSchema::DATE_INCIDENT)
                ->comment('Date/heure de survenue de l\'incident');

            $table->timestampTz(IncidentSchema::DATE_RAPPORT)
                ->comment('Date/heure de saisie du rapport');

            $table->timestampTz(IncidentSchema::DATE_PRISE_EN_COMPTE)
                ->nullable()
                ->comment('Date de prise en compte par la hiérarchie');

            $table->timestampTz(IncidentSchema::DATE_RESOLUTION)
                ->nullable()
                ->comment('Date de résolution de l\'incident');

            // Description détaillée
            $table->string(IncidentSchema::TITRE, 200)
                ->comment('Titre court de l\'incident');

            $table->text(IncidentSchema::DESCRIPTION)
                ->comment('Description détaillée de l\'incident');

            $table->text(IncidentSchema::ACTIONS_IMMEDIATES)
                ->nullable()
                ->comment('Actions entreprises immédiatement');

            $table->text(IncidentSchema::CONSEQUENCES)
                ->nullable()
                ->comment('Conséquences de l\'incident');

            // Personnes impliquées
            $table->jsonb(IncidentSchema::PERSONNES_IMPLIQUEES)
                ->nullable()
                ->comment('Employés et/ou clients impliqués');

            $table->jsonb(IncidentSchema::TEMOINS)
                ->nullable()
                ->comment('Témoins de l\'incident');

            // Dommages et coûts
            $table->boolean(IncidentSchema::DEGATS_MATERIELS)
                ->default(false);

            $table->integer(IncidentSchema::COUT_DEGATS_CENTIMES)
                ->default(0)
                ->comment('Coût estimé des dégâts en centimes');

            $table->string(IncidentSchema::DEVISE, 3)
                ->default('EUR');

            $table->boolean(IncidentSchema::ASSURANCE_IMPLIQUEE)
                ->default(false);

            $table->string(IncidentSchema::NUMERO_SINISTRE, 50)
                ->nullable()
                ->comment('Numéro de sinistre assurance');

            // Suivi et résolution
            $table->enum(IncidentSchema::STATUT, [
                'NOUVEAU',
                'EN_COURS',
                'EN_ATTENTE',
                'RESOLU',
                'CLOS',
                'ANNULE',
                'REPORTE',
            ])->default('NOUVEAU');

            $table->unsignedBigInteger(IncidentSchema::ASSIGNE_A_CONTRAT_ID)
                ->nullable()
                ->comment('Employé assigné pour résolution');

            $table->text(IncidentSchema::PLAN_ACTION)
                ->nullable()
                ->comment('Plan d\'action pour résolution');

            $table->text(IncidentSchema::RESOLUTION_FINALE)
                ->nullable()
                ->comment('Description de la résolution');

            // Prévention et amélioration
            $table->text(IncidentSchema::CAUSES_RACINES)
                ->nullable()
                ->comment('Analyse des causes racines');

            $table->text(IncidentSchema::MESURES_PREVENTIVES)
                ->nullable()
                ->comment('Mesures pour éviter la récurrence');

            $table->boolean(IncidentSchema::FORMATION_REQUISE)
                ->default(false);

            $table->text(IncidentSchema::RECOMMANDATIONS)
                ->nullable();

            // Conformité et réglementation
            $table->boolean(IncidentSchema::DECLARATION_OBLIGATOIRE)
                ->default(false)
                ->comment('Incident à déclarer aux autorités');

            $table->boolean(IncidentSchema::DECLARATION_EFFECTUEE)
                ->default(false);

            $table->timestampTz(IncidentSchema::DATE_DECLARATION)
                ->nullable();

            $table->string(IncidentSchema::ORGANISME_DECLARE, 100)
                ->nullable()
                ->comment('Organisme auquel l\'incident a été déclaré');

            // Documents et preuves
            $table->jsonb(IncidentSchema::PHOTOS_URLS)
                ->nullable()
                ->comment('URLs des photos de l\'incident');

            $table->jsonb(IncidentSchema::DOCUMENTS_URLS)
                ->nullable()
                ->comment('URLs des documents associés');

            $table->jsonb(IncidentSchema::VIDEOS_URLS)
                ->nullable()
                ->comment('URLs des vidéos de surveillance');

            // Métadonnées Desktop App US15
            $table->string(IncidentSchema::DEVICE_ID, 100)
                ->nullable()
                ->comment('ID du dispositif desktop utilisé');

            $table->string(IncidentSchema::APP_VERSION, 20)
                ->nullable()
                ->comment('Version de l\'app desktop');

            $table->ipAddress(IncidentSchema::IP_SAISIE)
                ->nullable()
                ->comment('IP du poste de saisie');

            $table->jsonb(IncidentSchema::METADONNEES_TECHNIQUE)
                ->nullable()
                ->comment('Métadonnées techniques de saisie');

            // Workflow et notifications
            $table->jsonb(IncidentSchema::HISTORIQUE_WORKFLOW)
                ->nullable()
                ->comment('Historique des changements de statut');

            $table->jsonb(IncidentSchema::NOTIFICATIONS_ENVOYEES)
                ->nullable()
                ->comment('Historique des notifications');

            // Notes et commentaires
            $table->text(IncidentSchema::NOTES_COMPLEMENTAIRES)
                ->nullable();

            $table->timestampsTz();

            // FK vers contrat rapporteur
            $table->foreign(IncidentSchema::CONTRAT_RAPPORTEUR_ID)
                ->references(ContratSchema::PRIMARY_KEY)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('restrict');

            // FK vers cinéma
            $table->foreign(IncidentSchema::CINEMA_DB_ID)
                ->references(CinemaSchema::PRIMARY_KEY)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(IncidentSchema::CINEMA_UUID)
                ->references(CinemaSchema::ID)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK vers salle (optionnelle)
            $table->foreign(IncidentSchema::SALLE_DB_ID)
                ->references(SalleSchema::PRIMARY_KEY)
                ->on(SalleSchema::FULL_TABLE)
                ->onDelete('set null');

            // FK vers séance (optionnelle)
            $table->foreign(IncidentSchema::SEANCE_DB_ID)
                ->references(SeanceSchema::PRIMARY_KEY)
                ->on(SeanceSchema::FULL_TABLE)
                ->onDelete('set null');

            // FK vers employé assigné
            $table->foreign(IncidentSchema::ASSIGNE_A_CONTRAT_ID)
                ->references(ContratSchema::PRIMARY_KEY)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('set null');

            // Index pour recherche UUID business
            $table->index(IncidentSchema::ID, IncidentSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(IncidentSchema::NUMERO_INCIDENT, IncidentSchema::INDEX_NUMERO);
            $table->index(IncidentSchema::CONTRAT_RAPPORTEUR_ID, IncidentSchema::INDEX_RAPPORTEUR);
            $table->index(IncidentSchema::CINEMA_DB_ID, IncidentSchema::INDEX_CINEMA);
            $table->index(IncidentSchema::TYPE_INCIDENT, IncidentSchema::INDEX_TYPE);
            $table->index(IncidentSchema::NIVEAU_GRAVITE, IncidentSchema::INDEX_GRAVITE);
            $table->index(IncidentSchema::NIVEAU_PRIORITE, IncidentSchema::INDEX_PRIORITE);
            $table->index(IncidentSchema::STATUT, IncidentSchema::INDEX_STATUT);
            $table->index(IncidentSchema::DATE_INCIDENT, IncidentSchema::INDEX_DATE_INCIDENT);
            $table->index(IncidentSchema::DATE_RAPPORT, IncidentSchema::INDEX_DATE_RAPPORT);

            // Index composés pour recherche avancée
            $table->index([IncidentSchema::CINEMA_DB_ID, IncidentSchema::STATUT], IncidentSchema::INDEX_CINEMA_STATUT);
            $table->index([IncidentSchema::TYPE_INCIDENT, IncidentSchema::NIVEAU_GRAVITE], IncidentSchema::INDEX_TYPE_GRAVITE);
            $table->index([IncidentSchema::DATE_INCIDENT, IncidentSchema::STATUT], IncidentSchema::INDEX_DATE_STATUT);
            $table->index([IncidentSchema::ASSIGNE_A_CONTRAT_ID, IncidentSchema::STATUT], IncidentSchema::INDEX_ASSIGNE_STATUT);

            // Contraintes métier (commentées car non supportées par Laravel Blueprint)
            // $table->check('date_rapport >= date_incident', IncidentSchema::CHECK_DATES_RAPPORT_COHERENTES);
            // $table->check('date_prise_en_compte IS NULL OR date_prise_en_compte >= date_rapport', IncidentSchema::CHECK_DATES_PRISE_EN_COMPTE_COHERENTES);
            // $table->check('date_resolution IS NULL OR date_resolution >= date_incident', IncidentSchema::CHECK_DATES_RESOLUTION_COHERENTES);
            // $table->check('cout_degats_centimes >= 0', IncidentSchema::CHECK_COUT_POSITIF);
        });

        // Index GIN sur JSONB pour queries sur personnes, documents et métadonnées
        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::PERSONNES_IMPLIQUEES,
            IncidentSchema::INDEX_PERSONNES_IMPLIQUEES
        );

        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::TEMOINS,
            IncidentSchema::INDEX_TEMOINS
        );

        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::PHOTOS_URLS,
            IncidentSchema::INDEX_PHOTOS
        );

        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::DOCUMENTS_URLS,
            IncidentSchema::INDEX_DOCUMENTS
        );

        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::METADONNEES_TECHNIQUE,
            IncidentSchema::INDEX_METADONNEES_TECHNIQUE
        );

        $helper->addGinIndex(
            IncidentSchema::FULL_TABLE,
            IncidentSchema::HISTORIQUE_WORKFLOW,
            IncidentSchema::INDEX_HISTORIQUE_WORKFLOW
        );

        // Commentaire sur la table
        $helper->addTableComment(
            IncidentSchema::FULL_TABLE,
            'Incidents et rapports employés pour Desktop App US15 avec workflow complet'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(IncidentSchema::FULL_TABLE);
    }
};
