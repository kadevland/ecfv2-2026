<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserRgpdConsentementSchema;

return new class extends Migration
{
    protected $connection = UserRgpdConsentementSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma user s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(UserRgpdConsentementSchema::CONNECTION));
        $helper->createSchemaIfNotExists(UserRgpdConsentementSchema::SCHEMA);

        Schema::create(UserRgpdConsentementSchema::FULL_TABLE, function (Blueprint $table) {
            $table->id(UserRgpdConsentementSchema::ID)
                ->primary();

            $table->id(UserRgpdConsentementSchema::USER_PROFIL_ID);

            // Type de consentement
            $table->enum(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, [
                'NEWSLETTER',
                'PROMOTIONS',
                'ALERTES_SEANCES',
                'COMMUNICATION_COMMERCIALE',
                'ENQUETES_SATISFACTION',
                'PARTENAIRES',
            ]);

            // Statut du consentement
            $table->boolean(UserRgpdConsentementSchema::CONSENTEMENT_DONNE)
                ->default(false);

            $table->timestampTz(UserRgpdConsentementSchema::DATE_CONSENTEMENT)
                ->nullable()
                ->comment('Date où le consentement a été donné');

            $table->timestampTz(UserRgpdConsentementSchema::DATE_RETRAIT)
                ->nullable()
                ->comment('Date où le consentement a été retiré');

            // Métadonnées RGPD
            $table->string(UserRgpdConsentementSchema::CANAL_COLLECTE, 50)
                ->nullable()
                ->comment('web, mobile, cinema, etc.');

            $table->ipAddress(UserRgpdConsentementSchema::IP_COLLECTE)
                ->nullable()
                ->comment('IP lors de la collecte du consentement');

            $table->text(UserRgpdConsentementSchema::CONTEXTE_COLLECTE)
                ->nullable()
                ->comment('Contexte de collecte du consentement');

            $table->timestampsTz();

            // FK vers profiles.user_profil avec CASCADE pour suppression RGPD
            $table->foreign(UserRgpdConsentementSchema::USER_PROFIL_ID)
                ->references(UserProfilSchema::PRIMARY_KEY)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('cascade');
            // ->comment('Suppression en cascade si profil supprimé (RGPD)'); // Non supporté

            // Contrainte unique : un seul consentement par type par utilisateur
            $table->unique([
                UserRgpdConsentementSchema::USER_PROFIL_ID,
                UserRgpdConsentementSchema::TYPE_CONSENTEMENT,
            ], 'unique_user_consent_type');

            // Indexes pour performance
            $table->index(UserRgpdConsentementSchema::USER_PROFIL_ID);
            $table->index(UserRgpdConsentementSchema::TYPE_CONSENTEMENT);
            $table->index(UserRgpdConsentementSchema::CONSENTEMENT_DONNE);
            $table->index(UserRgpdConsentementSchema::DATE_CONSENTEMENT);
        });

        // Commentaire sur la table
        $helper->addTableComment(
            UserRgpdConsentementSchema::FULL_TABLE,
            'Gestion des consentements RGPD pour newsletter, promotions et communications'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserRgpdConsentementSchema::FULL_TABLE);
    }
};
