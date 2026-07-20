<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

return new class extends Migration
{
    protected $connection = EmploiSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma employees s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(EmploiSchema::CONNECTION));
        $helper->createSchemaIfNotExists(EmploiSchema::SCHEMA);

        Schema::create(EmploiSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(EmploiSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(EmploiSchema::ID)
                ->unique();

            // Référence vers l'employé (profil FK pattern)
            $table->bigInteger(EmploiSchema::USER_PROFIL_KEY)->unsigned()->nullable();
            $table->uuid(EmploiSchema::USER_PROFIL_ID)->nullable();

            // Référence vers le cinéma (dual FK pattern)
            $table->bigInteger(EmploiSchema::CINEMA_KEY)->unsigned();
            $table->uuid(EmploiSchema::CINEMA_ID);

            // Informations du poste
            $table->string(EmploiSchema::TITRE_POSTE, 100);
            $table->text(EmploiSchema::DESCRIPTION)
                ->nullable();

            // Catégorie et niveau
            $table->enum(EmploiSchema::CATEGORIE, [
                'DIRECTION',
                'ENCADREMENT',
                'ACCUEIL_BILLETTERIE',
                'PROJECTION',
                'ENTRETIEN',
                'SECURITE',
                'TECHNIQUE',
                'ADMINISTRATIF',
                'ANIMATION',
                'RESTAURATION',
            ]);

            $table->enum(EmploiSchema::NIVEAU, [
                'STAGIAIRE',
                'JUNIOR',
                'CONFIRME',
                'SENIOR',
                'EXPERT',
                'RESPONSABLE',
                'MANAGER',
                'DIRECTEUR',
            ]);

            // Conditions d'emploi
            $table->enum(EmploiSchema::TYPE_CONTRAT, [
                'CDI',
                'CDD',
                'INTERIM',
                'STAGE',
                'APPRENTISSAGE',
                'FREELANCE',
            ]);

            $table->enum(EmploiSchema::TEMPS_TRAVAIL, [
                'TEMPS_PLEIN',
                'TEMPS_PARTIEL',
                'HORAIRES_VARIABLES',
                'SAISONNIER',
            ]);

            // Salaire et avantages
            $table->integer(EmploiSchema::SALAIRE_MIN_HT_CENTIMES)
                ->nullable()
                ->comment('Salaire minimum HT en centimes');

            $table->integer(EmploiSchema::SALAIRE_MAX_HT_CENTIMES)
                ->nullable()
                ->comment('Salaire maximum HT en centimes');

            $table->string(EmploiSchema::DEVISE, 3)
                ->default('EUR');

            $table->enum(EmploiSchema::PERIODICITE_SALAIRE, [
                'HORAIRE',
                'JOURNALIER',
                'MENSUEL',
                'ANNUEL',
            ])->default('MENSUEL');

            $table->jsonb(EmploiSchema::AVANTAGES)
                ->nullable()
                ->comment('Avantages en nature, tickets restaurant, etc.');

            // Compétences et prérequis
            $table->jsonb(EmploiSchema::COMPETENCES_REQUISES)
                ->nullable()
                ->comment('Compétences techniques requises');

            $table->jsonb(EmploiSchema::COMPETENCES_SOUHAITEES)
                ->nullable()
                ->comment('Compétences supplémentaires appréciées');

            $table->text(EmploiSchema::FORMATIONS_REQUISES)
                ->nullable();

            $table->integer(EmploiSchema::EXPERIENCE_MINIMUM_MOIS)
                ->default(0)
                ->comment('Expérience minimum en mois');

            // Horaires et planning
            $table->time(EmploiSchema::HEURE_DEBUT_TYPE)
                ->nullable()
                ->comment('Heure de début typique');

            $table->time(EmploiSchema::HEURE_FIN_TYPE)
                ->nullable()
                ->comment('Heure de fin typique');

            $table->jsonb(EmploiSchema::JOURS_TRAVAIL)
                ->nullable()
                ->comment('Jours de travail typiques');

            $table->boolean(EmploiSchema::TRAVAIL_WEEKEND)
                ->default(false);

            $table->boolean(EmploiSchema::TRAVAIL_FERIES)
                ->default(false);

            $table->boolean(EmploiSchema::TRAVAIL_SOIREE)
                ->default(false);

            // Responsabilités et hiérarchie
            $table->text(EmploiSchema::RESPONSABILITES)
                ->nullable();

            $table->boolean(EmploiSchema::ENCADREMENT_EQUIPE)
                ->default(false);

            $table->integer(EmploiSchema::NOMBRE_PERSONNES_ENCADREES)
                ->default(0);

            $table->unsignedBigInteger(EmploiSchema::RESPONSABLE_HIERARCHIQUE_ID)
                ->nullable()
                ->comment('ID du poste du responsable');

            // Statut et gestion
            $table->enum(EmploiSchema::STATUT, [
                'ACTIF',
                'SUSPENDU',
                'POURVUE',
                'SUPPRIME',
            ])->default('ACTIF');

            $table->boolean(EmploiSchema::RECRUTEMENT_OUVERT)
                ->default(false);

            $table->date(EmploiSchema::DATE_CREATION_POSTE);

            $table->date(EmploiSchema::DATE_FERMETURE_POSTE)
                ->nullable();

            // Date d'embauche
            $table->date(EmploiSchema::DATE_EMBAUCHE)
                ->nullable()
                ->comment('Date d\'embauche de l\'employé');

            // Informations RH
            $table->string(EmploiSchema::CODE_POSTE, 20)
                ->nullable()
                ->comment('Code RH interne');

            $table->string(EmploiSchema::CLASSIFICATION_CONVENTION, 50)
                ->nullable()
                ->comment('Classification selon convention collective');

            $table->text(EmploiSchema::NOTES_RH)
                ->nullable();

            $table->timestampsTz();

            // FK vers user profil (dual FK pattern)
            $table->foreign(EmploiSchema::USER_PROFIL_KEY)
                ->references(UserProfilSchema::PRIMARY_KEY)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('set null');

            $table->foreign(EmploiSchema::USER_PROFIL_ID)
                ->references(UserProfilSchema::ID)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('set null');

            // FK vers cinéma
            $table->foreign(EmploiSchema::CINEMA_KEY)
                ->references(CinemaSchema::PRIMARY_KEY)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(EmploiSchema::CINEMA_ID)
                ->references(CinemaSchema::ID)
                ->on(CinemaSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK auto-référentielle pour hiérarchie
            $table->foreign(EmploiSchema::RESPONSABLE_HIERARCHIQUE_ID)
                ->references(EmploiSchema::PRIMARY_KEY)
                ->on(EmploiSchema::FULL_TABLE)
                ->onDelete('set null');

            // Index pour recherche UUID business
            $table->index(EmploiSchema::ID, EmploiSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(EmploiSchema::USER_PROFIL_KEY, 'idx_emploi_user_profil_key');
            $table->index(EmploiSchema::USER_PROFIL_ID, 'idx_emploi_user_profil_id');
            $table->index(EmploiSchema::CINEMA_KEY, EmploiSchema::INDEX_CINEMA);
            $table->index(EmploiSchema::TITRE_POSTE, EmploiSchema::INDEX_TITRE);
            $table->index(EmploiSchema::CATEGORIE, EmploiSchema::INDEX_CATEGORIE);
            $table->index(EmploiSchema::NIVEAU, EmploiSchema::INDEX_NIVEAU);
            $table->index(EmploiSchema::TYPE_CONTRAT, EmploiSchema::INDEX_TYPE_CONTRAT);
            $table->index(EmploiSchema::STATUT, EmploiSchema::INDEX_STATUT);
            $table->index(EmploiSchema::RECRUTEMENT_OUVERT, EmploiSchema::INDEX_RECRUTEMENT);

            // Index composés pour recherche avancée
            $table->index([EmploiSchema::CINEMA_KEY, EmploiSchema::STATUT], EmploiSchema::INDEX_CINEMA_STATUT);
            $table->index([EmploiSchema::CATEGORIE, EmploiSchema::RECRUTEMENT_OUVERT], EmploiSchema::INDEX_CATEGORIE_RECRUTEMENT);
            $table->index([EmploiSchema::TYPE_CONTRAT, EmploiSchema::STATUT], EmploiSchema::INDEX_CONTRAT_STATUT);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries compétences et avantages
        $helper->addGinIndex(
            EmploiSchema::FULL_TABLE,
            EmploiSchema::COMPETENCES_REQUISES,
            EmploiSchema::INDEX_COMPETENCES_REQUISES
        );

        $helper->addGinIndex(
            EmploiSchema::FULL_TABLE,
            EmploiSchema::COMPETENCES_SOUHAITEES,
            EmploiSchema::INDEX_COMPETENCES_SOUHAITEES
        );

        $helper->addGinIndex(
            EmploiSchema::FULL_TABLE,
            EmploiSchema::AVANTAGES,
            EmploiSchema::INDEX_AVANTAGES
        );

        $helper->addGinIndex(
            EmploiSchema::FULL_TABLE,
            EmploiSchema::JOURS_TRAVAIL,
            EmploiSchema::INDEX_JOURS_TRAVAIL
        );

        // Commentaire sur la table
        $helper->addTableComment(
            EmploiSchema::FULL_TABLE,
            'Définition des postes et emplois avec salaires et compétences'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(EmploiSchema::FULL_TABLE);
    }
};
