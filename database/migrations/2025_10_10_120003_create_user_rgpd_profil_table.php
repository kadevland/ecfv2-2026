<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Profiles\UserRgpdProfilSchema;

return new class extends Migration
{
    protected $connection = UserRgpdProfilSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma user s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(UserRgpdProfilSchema::CONNECTION));
        $helper->createSchemaIfNotExists(UserRgpdProfilSchema::SCHEMA);

        Schema::create(UserRgpdProfilSchema::FULL_TABLE, function (Blueprint $table) {
            $table->id(UserRgpdProfilSchema::ID)
                ->primary();

            $table->uuid(UserRgpdProfilSchema::USER_UUID_ORIGINAL)
                ->unique()
                ->comment('UUID original de l\'utilisateur supprimé');

            // Données de substitution RGPD
            $table->string(UserRgpdProfilSchema::NOM_SUBSTITUTION, 100)
                ->default('Utilisateur Supprimé');
            $table->string(UserRgpdProfilSchema::PRENOM_SUBSTITUTION, 100)
                ->default('RGPD');
            $table->string(UserRgpdProfilSchema::EMAIL_SUBSTITUTION, 320)
                ->unique()
                ->comment('Email générique pour remplacer l\'original');

            // Métadonnées de suppression
            $table->timestampTz(UserRgpdProfilSchema::DATE_SUPPRESSION_DEMANDEE)
                ->comment('Date de la demande de suppression RGPD');
            $table->timestampTz(UserRgpdProfilSchema::DATE_SUPPRESSION_EFFECTIVE)
                ->comment('Date effective de la suppression des données');
            $table->enum(UserRgpdProfilSchema::RAISON_SUPPRESSION, [
                'DROIT_OUBLI',
                'DEMANDE_CLIENT',
                'INACTIVITE',
                'VIOLATION_CGU',
                'AUTRE',
            ])->default('DROIT_OUBLI');

            // Informations de traitement
            $table->string(UserRgpdProfilSchema::OPERATEUR_SUPPRESSION, 100)
                ->nullable()
                ->comment('Employé qui a traité la demande');
            $table->text(UserRgpdProfilSchema::COMMENTAIRE_INTERNE)
                ->nullable()
                ->comment('Notes internes sur la suppression');

            // Données business minimales conservées
            $table->boolean(UserRgpdProfilSchema::AVAIT_RESERVATIONS)
                ->default(false)
                ->comment('Indique si l\'utilisateur avait des réservations');
            $table->integer(UserRgpdProfilSchema::NOMBRE_RESERVATIONS_HISTORIQUE)
                ->default(0)
                ->comment('Nombre total de réservations pour statistiques');

            $table->timestampsTz();

            // Indexes pour performance
            $table->index(UserRgpdProfilSchema::USER_UUID_ORIGINAL);
            $table->index(UserRgpdProfilSchema::DATE_SUPPRESSION_EFFECTIVE);
            $table->index(UserRgpdProfilSchema::RAISON_SUPPRESSION);
        });

        // Commentaire sur la table
        $helper->addTableComment(
            UserRgpdProfilSchema::FULL_TABLE,
            'Profils de substitution RGPD pour les utilisateurs ayant exercé leur droit de suppression'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserRgpdProfilSchema::FULL_TABLE);
    }
};
