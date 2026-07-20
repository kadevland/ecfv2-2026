<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

return new class extends Migration
{
    protected $connection = ReservationSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma reservations s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(ReservationSchema::CONNECTION));
        $helper->createSchemaIfNotExists(ReservationSchema::SCHEMA);

        Schema::create(ReservationSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(ReservationSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(ReservationSchema::ID)
                ->unique();

            // Numéro de réservation unique pour clients
            $table->string(ReservationSchema::NUMERO_RESERVATION, 20)
                ->unique()
                ->comment('Numéro visible par le client (ex: CIN-2025-001234)');

            // Références vers utilisateur et séance (dual FK)
            $table->bigInteger(ReservationSchema::USER_KEY)->unsigned();
            $table->uuid(ReservationSchema::USER_ID);
            $table->bigInteger(ReservationSchema::SEANCE_KEY)->unsigned();
            $table->uuid(ReservationSchema::SEANCE_ID);

            // Informations de réservation
            $table->integer(ReservationSchema::NOMBRE_PLACES)
                ->comment('Nombre total de places réservées');

            $table->jsonb(ReservationSchema::DETAILS_PLACES)
                ->comment('Détails des places (rangée, numéro, type)');

            // Statut de la réservation
            $table->enum(ReservationSchema::STATUT, [
                'EN_ATTENTE_PAIEMENT',
                'CONFIRMEE',
                'PAYEE',
                'UTILISEE',
                'ANNULEE',
                'EXPIREE',
                'REMBOURSEE',
            ])->default('EN_ATTENTE_PAIEMENT');

            // Tarification MoneyPHP
            $table->integer(ReservationSchema::PRIX_UNITAIRE_HT_CENTIMES)
                ->comment('Prix unitaire HT en centimes');

            $table->integer(ReservationSchema::PRIX_TOTAL_HT_CENTIMES)
                ->comment('Prix total HT en centimes');

            $table->string(ReservationSchema::DEVISE, 3)
                ->default('EUR');

            $table->integer(ReservationSchema::TAUX_TVA_BASIS_POINTS)
                ->comment('Taux TVA en basis points');

            $table->integer(ReservationSchema::PRIX_TOTAL_TTC_CENTIMES)
                ->comment('Prix total TTC en centimes');

            // Gestion temporelle
            $table->timestampTz(ReservationSchema::DATE_RESERVATION);

            $table->timestampTz(ReservationSchema::DATE_EXPIRATION)
                ->nullable()
                ->comment('Date limite pour confirmer/payer');

            $table->timestampTz(ReservationSchema::DATE_CONFIRMATION)
                ->nullable();

            $table->timestampTz(ReservationSchema::DATE_UTILISATION)
                ->nullable()
                ->comment('Date d\'utilisation des billets');

            // Informations client
            $table->string(ReservationSchema::EMAIL_CONFIRMATION, 320)
                ->comment('Email pour les confirmations');

            $table->string(ReservationSchema::TELEPHONE_CONTACT, 20)
                ->nullable();

            // Codes et tokens
            $table->string(ReservationSchema::CODE_CONFIRMATION, 10)
                ->unique()
                ->comment('Code court pour confirmation (ex: AB123CD)');

            $table->string(ReservationSchema::TOKEN_SECURITE, 64)
                ->unique()
                ->comment('Token sécurisé pour annulation/modification');

            // Métadonnées commerciales
            $table->string(ReservationSchema::CANAL_RESERVATION, 50)
                ->default('WEB')
                ->comment('WEB, MOBILE, GUICHET, TELEPHONE');

            $table->string(ReservationSchema::CODE_PROMOTION, 50)
                ->nullable();

            $table->integer(ReservationSchema::REMISE_CENTIMES)
                ->default(0)
                ->comment('Montant de remise appliquée');

            // Informations techniques
            $table->ipAddress(ReservationSchema::IP_RESERVATION)
                ->nullable();

            $table->string(ReservationSchema::USER_AGENT, 500)
                ->nullable();

            $table->jsonb(ReservationSchema::METADONNEES_RESERVATION)
                ->nullable()
                ->comment('Données additionnelles (browser, device, etc.)');

            // Notes et commentaires
            $table->text(ReservationSchema::NOTES_CLIENT)
                ->nullable();

            $table->text(ReservationSchema::NOTES_INTERNES)
                ->nullable();

            $table->timestampsTz();

            // FK vers user profil
            $table->foreign(ReservationSchema::USER_KEY)
                ->references(UserProfilSchema::PRIMARY_KEY)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('restrict');

            $table->foreign(ReservationSchema::USER_ID)
                ->references(UserProfilSchema::ID)
                ->on(UserProfilSchema::FULL_TABLE)
                ->onDelete('restrict');

            // FK vers séance
            $table->foreign(ReservationSchema::SEANCE_KEY)
                ->references(SeanceSchema::PRIMARY_KEY)
                ->on(SeanceSchema::FULL_TABLE)
                ->onDelete('restrict');

            $table->foreign(ReservationSchema::SEANCE_ID)
                ->references(SeanceSchema::ID)
                ->on(SeanceSchema::FULL_TABLE)
                ->onDelete('restrict');

            // Index pour recherche UUID business
            $table->index(ReservationSchema::ID, ReservationSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(ReservationSchema::NUMERO_RESERVATION, ReservationSchema::INDEX_NUMERO);
            $table->index(ReservationSchema::USER_KEY, ReservationSchema::INDEX_USER);
            $table->index(ReservationSchema::SEANCE_KEY, ReservationSchema::INDEX_SEANCE);
            $table->index(ReservationSchema::STATUT, ReservationSchema::INDEX_STATUT);
            $table->index(ReservationSchema::DATE_RESERVATION, ReservationSchema::INDEX_DATE_RESERVATION);
            $table->index(ReservationSchema::DATE_EXPIRATION, ReservationSchema::INDEX_DATE_EXPIRATION);
            $table->index(ReservationSchema::EMAIL_CONFIRMATION, ReservationSchema::INDEX_EMAIL);
            $table->index(ReservationSchema::CODE_CONFIRMATION, ReservationSchema::INDEX_CODE_CONFIRMATION);

            // Index composés pour recherche avancée
            $table->index([ReservationSchema::USER_KEY, ReservationSchema::STATUT], ReservationSchema::INDEX_USER_STATUT);
            $table->index([ReservationSchema::SEANCE_KEY, ReservationSchema::STATUT], ReservationSchema::INDEX_SEANCE_STATUT);
            $table->index([ReservationSchema::DATE_RESERVATION, ReservationSchema::STATUT], ReservationSchema::INDEX_DATE_STATUT);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries places et métadonnées
        $helper->addGinIndex(
            ReservationSchema::FULL_TABLE,
            ReservationSchema::DETAILS_PLACES,
            ReservationSchema::INDEX_DETAILS_PLACES
        );

        $helper->addGinIndex(
            ReservationSchema::FULL_TABLE,
            ReservationSchema::METADONNEES_RESERVATION,
            ReservationSchema::INDEX_METADONNEES
        );

        // Commentaire sur la table
        $helper->addTableComment(
            ReservationSchema::FULL_TABLE,
            'Réservations de places avec tarification MoneyPHP et suivi complet'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ReservationSchema::FULL_TABLE);
    }
};
