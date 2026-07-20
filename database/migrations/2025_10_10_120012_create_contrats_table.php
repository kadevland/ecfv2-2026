<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

return new class extends Migration
{
    protected $connection = ContratSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma employees s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(ContratSchema::CONNECTION));
        $helper->createSchemaIfNotExists(ContratSchema::SCHEMA);

        Schema::create(ContratSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(ContratSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(ContratSchema::ID)
                ->unique();

            // Numéro de contrat unique
            $table->string(ContratSchema::NUMERO_CONTRAT, 30)
                ->unique()
                ->comment('Numéro unique du contrat (ex: CTR-2025-001234)');

            // Références vers employé et emploi
            $table->unsignedBigInteger(ContratSchema::USER_PROFIL_ID);
            $table->uuid(ContratSchema::USER_UUID);
            $table->unsignedBigInteger(ContratSchema::EMPLOI_DB_ID);
            $table->uuid(ContratSchema::EMPLOI_UUID);

            // Type et statut du contrat
            $table->enum(ContratSchema::TYPE_CONTRAT, [
                'CDI',
                'CDD',
                'INTERIM',
                'STAGE',
                'APPRENTISSAGE',
                'FREELANCE',
                'SAISONNIER',
            ]);

            $table->enum(ContratSchema::STATUT, [
                'BROUILLON',
                'EN_ATTENTE_SIGNATURE',
                'SIGNE',
                'ACTIF',
                'SUSPENDU',
                'TERMINE',
                'ROMPU',
                'ANNULE',
            ])->default('BROUILLON');

            // Périodes du contrat
            $table->date(ContratSchema::DATE_DEBUT);
            $table->date(ContratSchema::DATE_FIN)
                ->nullable()
                ->comment('NULL pour CDI');

            $table->date(ContratSchema::DATE_SIGNATURE)
                ->nullable();

            $table->date(ContratSchema::DATE_FIN_PERIODE_ESSAI)
                ->nullable();

            // Temps de travail
            $table->enum(ContratSchema::TEMPS_TRAVAIL, [
                'TEMPS_PLEIN',
                'TEMPS_PARTIEL',
                'HORAIRES_VARIABLES',
            ])->default('TEMPS_PLEIN');

            $table->decimal(ContratSchema::HEURES_HEBDOMADAIRES, 5, 2)
                ->default(35.00)
                ->comment('Nombre d\'heures par semaine');

            $table->integer(ContratSchema::JOURS_CONGES_ANNUELS)
                ->default(25)
                ->comment('Jours de congés payés par an');

            // Rémunération MoneyPHP
            $table->integer(ContratSchema::SALAIRE_BRUT_HT_CENTIMES)
                ->comment('Salaire brut HT en centimes');

            $table->string(ContratSchema::DEVISE, 3)
                ->default('EUR');

            $table->enum(ContratSchema::PERIODICITE_SALAIRE, [
                'HORAIRE',
                'JOURNALIER',
                'MENSUEL',
                'ANNUEL',
            ])->default('MENSUEL');

            // Primes et avantages MoneyPHP
            $table->integer(ContratSchema::PRIME_ANCIENNETE_CENTIMES)
                ->default(0)
                ->comment('Prime d\'ancienneté en centimes');

            $table->integer(ContratSchema::PRIME_PERFORMANCE_CENTIMES)
                ->default(0)
                ->comment('Prime de performance en centimes');

            $table->integer(ContratSchema::AVANTAGES_NATURE_CENTIMES)
                ->default(0)
                ->comment('Valeur avantages en nature en centimes');

            $table->jsonb(ContratSchema::DETAIL_AVANTAGES)
                ->nullable()
                ->comment('Détail des avantages (tickets resto, transport, etc.)');

            // Clause et conditions
            $table->boolean(ContratSchema::CLAUSE_NON_CONCURRENCE)
                ->default(false);

            $table->boolean(ContratSchema::CLAUSE_CONFIDENTIALITE)
                ->default(true);

            $table->boolean(ContratSchema::CLAUSE_MOBILITE)
                ->default(false);

            $table->integer(ContratSchema::PREAVIS_JOURS)
                ->nullable()
                ->comment('Préavis de démission/licenciement en jours');

            // Horaires et planning
            $table->jsonb(ContratSchema::HORAIRES_STANDARDS)
                ->nullable()
                ->comment('Horaires de travail standards par jour');

            $table->boolean(ContratSchema::TRAVAIL_WEEKEND)
                ->default(false);

            $table->boolean(ContratSchema::TRAVAIL_FERIES)
                ->default(false);

            $table->boolean(ContratSchema::TRAVAIL_NUIT)
                ->default(false);

            // Formation et évolution
            $table->integer(ContratSchema::BUDGET_FORMATION_CENTIMES)
                ->default(0)
                ->comment('Budget formation annuel en centimes');

            $table->text(ContratSchema::OBJECTIFS_POSTE)
                ->nullable();

            $table->date(ContratSchema::DATE_PROCHAINE_EVALUATION)
                ->nullable();

            // Informations légales
            $table->string(ContratSchema::NUMERO_SECURITE_SOCIALE, 15)
                ->nullable();

            $table->string(ContratSchema::CONVENTION_COLLECTIVE, 100)
                ->nullable();

            $table->string(ContratSchema::CLASSIFICATION_POSTE, 20)
                ->nullable();

            $table->integer(ContratSchema::COEFFICIENT_HIERARCHIQUE)
                ->nullable();

            // Gestion des modifications
            $table->integer(ContratSchema::VERSION)
                ->default(1)
                ->comment('Version du contrat pour les avenants');

            $table->unsignedBigInteger(ContratSchema::CONTRAT_PARENT_ID)
                ->nullable()
                ->comment('ID du contrat original pour les avenants');

            $table->text(ContratSchema::MOTIF_MODIFICATION)
                ->nullable()
                ->comment('Motif de l\'avenant');

            // Fin de contrat
            $table->date(ContratSchema::DATE_FIN_EFFECTIVE)
                ->nullable()
                ->comment('Date réelle de fin de contrat');

            $table->enum(ContratSchema::MOTIF_FIN, [
                'ARRIVEE_TERME',
                'DEMISSION',
                'LICENCIEMENT_ECONOMIQUE',
                'LICENCIEMENT_FAUTE',
                'RUPTURE_CONVENTIONNELLE',
                'RETRAITE',
                'DECES',
                'INAPTITUDE',
                'ABANDON_POSTE',
            ])->nullable();

            $table->text(ContratSchema::COMMENTAIRE_FIN)
                ->nullable();

            // Documents et signatures
            $table->string(ContratSchema::DOCUMENT_PDF_URL, 500)
                ->nullable()
                ->comment('URL du contrat PDF signé');

            $table->string(ContratSchema::SIGNATURE_EMPLOYE_URL, 500)
                ->nullable();

            $table->string(ContratSchema::SIGNATURE_EMPLOYEUR_URL, 500)
                ->nullable();

            $table->timestampTz(ContratSchema::DATE_SIGNATURE_EMPLOYE)
                ->nullable();

            $table->timestampTz(ContratSchema::DATE_SIGNATURE_EMPLOYEUR)
                ->nullable();

            // Notes et métadonnées
            $table->text(ContratSchema::NOTES_RH)
                ->nullable();

            $table->jsonb(ContratSchema::METADONNEES_CONTRAT)
                ->nullable()
                ->comment('Métadonnées additionnelles');

            $table->timestampsTz();

            // FK vers user profil
            $table->foreign(ContratSchema::USER_PROFIL_ID)
                ->references(UserProfilSchema::PRIMARY_KEY)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('restrict');

            // FK vers emploi
            $table->foreign(ContratSchema::EMPLOI_DB_ID)
                ->references(EmploiSchema::PRIMARY_KEY)
                ->on(EmploiSchema::FULL_TABLE)
                ->onDelete('restrict');

            $table->foreign(ContratSchema::EMPLOI_UUID)
                ->references(EmploiSchema::ID)
                ->on(EmploiSchema::FULL_TABLE)
                ->onDelete('restrict');

            // FK auto-référentielle pour avenants
            $table->foreign(ContratSchema::CONTRAT_PARENT_ID)
                ->references(ContratSchema::PRIMARY_KEY)
                ->on(ContratSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour recherche UUID business
            $table->index(ContratSchema::ID, ContratSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(ContratSchema::NUMERO_CONTRAT, ContratSchema::INDEX_NUMERO);
            $table->index(ContratSchema::USER_PROFIL_ID, ContratSchema::INDEX_USER);
            $table->index(ContratSchema::EMPLOI_DB_ID, ContratSchema::INDEX_EMPLOI);
            $table->index(ContratSchema::TYPE_CONTRAT, ContratSchema::INDEX_TYPE);
            $table->index(ContratSchema::STATUT, ContratSchema::INDEX_STATUT);
            $table->index(ContratSchema::DATE_DEBUT, ContratSchema::INDEX_DATE_DEBUT);
            $table->index(ContratSchema::DATE_FIN, ContratSchema::INDEX_DATE_FIN);
            $table->index(ContratSchema::VERSION, ContratSchema::INDEX_VERSION);

            // Index composés pour recherche avancée
            $table->index([ContratSchema::USER_PROFIL_ID, ContratSchema::STATUT], ContratSchema::INDEX_USER_STATUT);
            $table->index([ContratSchema::TYPE_CONTRAT, ContratSchema::STATUT], ContratSchema::INDEX_TYPE_STATUT);
            $table->index([ContratSchema::DATE_DEBUT, ContratSchema::DATE_FIN], ContratSchema::INDEX_PERIODE);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries avantages et métadonnées
        $helper->addGinIndex(
            ContratSchema::FULL_TABLE,
            ContratSchema::DETAIL_AVANTAGES,
            ContratSchema::INDEX_DETAIL_AVANTAGES
        );

        $helper->addGinIndex(
            ContratSchema::FULL_TABLE,
            ContratSchema::HORAIRES_STANDARDS,
            ContratSchema::INDEX_HORAIRES_STANDARDS
        );

        $helper->addGinIndex(
            ContratSchema::FULL_TABLE,
            ContratSchema::METADONNEES_CONTRAT,
            ContratSchema::INDEX_METADONNEES_CONTRAT
        );

        // Commentaire sur la table
        $helper->addTableComment(
            ContratSchema::FULL_TABLE,
            'Contrats employés avec salaires MoneyPHP et gestion des avenants'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ContratSchema::FULL_TABLE);
    }
};
