<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;
use App\Infrastructure\Database\Schemas\Employees\PlanningSchema;

return new class extends Migration
{
    protected $connection = PlanningSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma employees s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(PlanningSchema::CONNECTION));
        $helper->createSchemaIfNotExists(PlanningSchema::SCHEMA);

        Schema::create(PlanningSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(PlanningSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(PlanningSchema::ID)
                ->unique();

            // Références vers contrat et cinéma
            $table->bigInteger(PlanningSchema::CONTRAT_KEY)->unsigned();
            $table->uuid(PlanningSchema::CONTRAT_ID);
            $table->bigInteger(PlanningSchema::CINEMA_KEY)->unsigned();
            $table->uuid(PlanningSchema::CINEMA_ID);

            // Période de planning
            $table->date(PlanningSchema::DATE_PLANNING);
            $table->integer(PlanningSchema::SEMAINE_ANNEE)
                ->comment('Numéro de semaine dans l\'année (1-53)');
            $table->integer(PlanningSchema::ANNEE);

            // Horaires de travail
            $table->time(PlanningSchema::HEURE_DEBUT)
                ->nullable()
                ->comment('NULL = jour de repos');

            $table->time(PlanningSchema::HEURE_FIN)
                ->nullable();

            $table->time(PlanningSchema::PAUSE_DEBUT)
                ->nullable();

            $table->time(PlanningSchema::PAUSE_FIN)
                ->nullable();

            // Calculs de temps
            $table->decimal(PlanningSchema::HEURES_TRAVAILLEES, 5, 2)
                ->default(0.00)
                ->comment('Heures de travail effectives');

            $table->decimal(PlanningSchema::HEURES_PAUSE, 5, 2)
                ->default(0.00)
                ->comment('Heures de pause');

            $table->decimal(PlanningSchema::HEURES_SUPPLEMENTAIRES, 5, 2)
                ->default(0.00)
                ->comment('Heures supplémentaires');

            // Type de service
            $table->enum(PlanningSchema::TYPE_SERVICE, [
                'MATIN',
                'APRES_MIDI',
                'SOIREE',
                'NUIT',
                'JOUR_COMPLET',
                'REPOS',
                'CONGE',
                'MALADIE',
                'FORMATION',
            ]);

            $table->enum(PlanningSchema::STATUT, [
                'PLANIFIE',
                'CONFIRME',
                'EN_COURS',
                'TERMINE',
                'ABSENCE',
                'RETARD',
                'MODIFIE',
            ])->default('PLANIFIE');

            // Poste et affectation
            $table->string(PlanningSchema::POSTE_ASSIGNE, 100)
                ->nullable()
                ->comment('Poste spécifique pour cette journée');

            $table->string(PlanningSchema::ZONE_AFFECTATION, 100)
                ->nullable()
                ->comment('Zone ou département d\'affectation');

            // Remplacement et substitution
            $table->unsignedBigInteger(PlanningSchema::REMPLACE_CONTRAT_ID)
                ->nullable()
                ->comment('ID du contrat remplacé');

            $table->text(PlanningSchema::MOTIF_REMPLACEMENT)
                ->nullable();

            $table->boolean(PlanningSchema::EST_REMPLACEMENT)
                ->default(false);

            // Gestion des absences
            $table->enum(PlanningSchema::TYPE_ABSENCE, [
                'CONGE_PAYE',
                'RTT',
                'MALADIE',
                'ACCIDENT_TRAVAIL',
                'CONGE_MATERNITE',
                'CONGE_PATERNITE',
                'FORMATION',
                'ABSENCE_EXCEPTIONNELLE',
                'GREVE',
            ])->nullable();

            $table->boolean(PlanningSchema::JUSTIFICATIF_REQUIS)
                ->default(false);

            $table->boolean(PlanningSchema::JUSTIFICATIF_FOURNI)
                ->default(false);

            // Pointage et présence
            $table->timestampTz(PlanningSchema::POINTAGE_ARRIVEE)
                ->nullable();

            $table->timestampTz(PlanningSchema::POINTAGE_DEPART)
                ->nullable();

            $table->timestampTz(PlanningSchema::POINTAGE_PAUSE_DEBUT)
                ->nullable();

            $table->timestampTz(PlanningSchema::POINTAGE_PAUSE_FIN)
                ->nullable();

            // Validation et approbation
            $table->boolean(PlanningSchema::VALIDE_EMPLOYE)
                ->default(false);

            $table->boolean(PlanningSchema::VALIDE_MANAGER)
                ->default(false);

            $table->timestampTz(PlanningSchema::DATE_VALIDATION_EMPLOYE)
                ->nullable();

            $table->timestampTz(PlanningSchema::DATE_VALIDATION_MANAGER)
                ->nullable();

            $table->unsignedBigInteger(PlanningSchema::VALIDATEUR_MANAGER_ID)
                ->nullable()
                ->comment('ID du manager qui a validé');

            // Notes et commentaires
            $table->text(PlanningSchema::NOTES_EMPLOYE)
                ->nullable();

            $table->text(PlanningSchema::NOTES_MANAGER)
                ->nullable();

            $table->text(PlanningSchema::NOTES_RH)
                ->nullable();

            // Informations de modification
            $table->timestampTz(PlanningSchema::DATE_DERNIERE_MODIFICATION)
                ->nullable();

            $table->unsignedBigInteger(PlanningSchema::MODIFIE_PAR_USER_ID)
                ->nullable();

            $table->text(PlanningSchema::HISTORIQUE_MODIFICATIONS)
                ->nullable()
                ->comment('JSON des modifications successives');

            // Métadonnées spéciales
            $table->boolean(PlanningSchema::JOUR_FERIE)
                ->default(false);

            $table->boolean(PlanningSchema::WEEK_END)
                ->default(false);

            $table->string(PlanningSchema::PERIODE_SPECIALE, 50)
                ->nullable()
                ->comment('Vacances scolaires, festival, etc.');

            // Calculs financiers (pour paie)
            $table->decimal(PlanningSchema::TAUX_HORAIRE_BASE, 10, 4)
                ->nullable()
                ->comment('Taux horaire de base pour cette période');

            $table->decimal(PlanningSchema::MAJORATION_COEFFICIENT, 5, 4)
                ->default(1.0000)
                ->comment('Coefficient de majoration (dimanche, nuit, etc.)');

            $table->timestampsTz();

            // FK vers contrat
            $table->foreign(PlanningSchema::CONTRAT_KEY)
                ->references(ContratSchema::PRIMARY_KEY)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(PlanningSchema::CONTRAT_ID)
                ->references(ContratSchema::ID)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK vers cinéma
            $table->foreign(PlanningSchema::CINEMA_KEY)
                ->references(CinemaSchema::PRIMARY_KEY)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(PlanningSchema::CINEMA_ID)
                ->references(CinemaSchema::ID)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK vers contrat remplacé
            $table->foreign(PlanningSchema::REMPLACE_CONTRAT_ID)
                ->references(ContratSchema::PRIMARY_KEY)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('set null');

            // Index pour recherche UUID business
            $table->index(PlanningSchema::ID, PlanningSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(PlanningSchema::CONTRAT_KEY, PlanningSchema::INDEX_CONTRAT);
            $table->index(PlanningSchema::CINEMA_KEY, PlanningSchema::INDEX_CINEMA);
            $table->index(PlanningSchema::DATE_PLANNING, PlanningSchema::INDEX_DATE);
            $table->index(PlanningSchema::SEMAINE_ANNEE, PlanningSchema::INDEX_SEMAINE);
            $table->index(PlanningSchema::ANNEE, PlanningSchema::INDEX_ANNEE);
            $table->index(PlanningSchema::TYPE_SERVICE, PlanningSchema::INDEX_TYPE_SERVICE);
            $table->index(PlanningSchema::STATUT, PlanningSchema::INDEX_STATUT);
            $table->index(PlanningSchema::EST_REMPLACEMENT, PlanningSchema::INDEX_REMPLACEMENT);

            // Index composés pour recherche avancée
            $table->index([PlanningSchema::CONTRAT_KEY, PlanningSchema::DATE_PLANNING], PlanningSchema::INDEX_CONTRAT_DATE);
            $table->index([PlanningSchema::CINEMA_KEY, PlanningSchema::DATE_PLANNING], PlanningSchema::INDEX_CINEMA_DATE);
            $table->index([PlanningSchema::ANNEE, PlanningSchema::SEMAINE_ANNEE], PlanningSchema::INDEX_ANNEE_SEMAINE);
            $table->index([PlanningSchema::DATE_PLANNING, PlanningSchema::TYPE_SERVICE], PlanningSchema::INDEX_DATE_SERVICE);
            $table->index([PlanningSchema::STATUT, PlanningSchema::DATE_PLANNING], PlanningSchema::INDEX_STATUT_DATE);

            // Contrainte unique : un seul planning par contrat par jour
            $table->unique([
                PlanningSchema::CONTRAT_KEY,
                PlanningSchema::DATE_PLANNING,
            ], PlanningSchema::UNIQUE_CONTRAT_DATE);

            // Contraintes métier (commentées car non supportées par Laravel Blueprint)
            // $table->check('heure_fin IS NULL OR heure_debut IS NULL OR heure_fin > heure_debut', PlanningSchema::CHECK_HORAIRES_COHERENTS);
            // $table->check('pause_fin IS NULL OR pause_debut IS NULL OR pause_fin > pause_debut', PlanningSchema::CHECK_PAUSE_COHERENTE);
            // $table->check('heures_travaillees >= 0', PlanningSchema::CHECK_HEURES_POSITIVES);
            // $table->check('heures_pause >= 0', PlanningSchema::CHECK_PAUSE_POSITIVE);
            // $table->check('heures_supplementaires >= 0', PlanningSchema::CHECK_HEURES_SUP_POSITIVES);
            // $table->check('semaine_annee >= 1 AND semaine_annee <= 53', PlanningSchema::CHECK_SEMAINE_VALIDE);
            // $table->check('majoration_coefficient >= 0', PlanningSchema::CHECK_MAJORATION_POSITIVE);
        });

        // Commentaire sur la table
        $helper->addTableComment(
            PlanningSchema::FULL_TABLE,
            'Planning détaillé des employés avec pointage et calculs de paie'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(PlanningSchema::FULL_TABLE);
    }
};
