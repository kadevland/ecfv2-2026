<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Reservations\BilletSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

return new class extends Migration
{
    protected $connection = BilletSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma reservations s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(BilletSchema::CONNECTION));
        $helper->createSchemaIfNotExists(BilletSchema::SCHEMA);

        Schema::create(BilletSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(BilletSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(BilletSchema::ID)
                ->unique();

            // Numéro de billet unique
            $table->string(BilletSchema::NUMERO_BILLET, 30)
                ->unique()
                ->comment('Numéro unique du billet (ex: CIN-2025-001234-01)');

            // Référence vers la réservation
            $table->bigInteger(BilletSchema::RESERVATION_KEY)->unsigned();
            $table->uuid(BilletSchema::RESERVATION_ID);

            // Informations de place
            $table->string(BilletSchema::RANGEE, 10)
                ->comment('Numéro/lettre de rangée');

            $table->integer(BilletSchema::NUMERO_PLACE)
                ->comment('Numéro de la place dans la rangée');

            $table->enum(BilletSchema::TYPE_PLACE, [
                'STANDARD',
                'PREMIUM',
                'PMR',
                'ACCOMPAGNATEUR',
            ])->default('STANDARD');

            // QR Code pour US14 (Mobile App)
            $table->string(BilletSchema::QR_CODE_DATA, 500)
                ->unique()
                ->comment('Données encodées dans le QR code');

            $table->string(BilletSchema::QR_CODE_URL, 1000)
                ->nullable()
                ->comment('URL publique du QR code généré');

            $table->string(BilletSchema::QR_CODE_HASH, 64)
                ->unique()
                ->comment('Hash de vérification du QR code');

            // Statut du billet
            $table->enum(BilletSchema::STATUT, [
                'EMIS',
                'ENVOYE',
                'VALIDE',
                'UTILISE',
                'EXPIRE',
                'ANNULE',
                'REMBOURSE',
            ])->default('EMIS');

            // Tarification individuelle MoneyPHP
            $table->integer(BilletSchema::PRIX_HT_CENTIMES)
                ->comment('Prix HT du billet en centimes');

            $table->string(BilletSchema::DEVISE, 3)
                ->default('EUR');

            $table->integer(BilletSchema::TAUX_TVA_BASIS_POINTS)
                ->comment('Taux TVA en basis points');

            $table->integer(BilletSchema::PRIX_TTC_CENTIMES)
                ->comment('Prix TTC du billet en centimes');

            // Gestion temporelle
            $table->timestampTz(BilletSchema::DATE_EMISSION);

            $table->timestampTz(BilletSchema::DATE_ENVOI)
                ->nullable()
                ->comment('Date d\'envoi par email/SMS');

            $table->timestampTz(BilletSchema::DATE_VALIDATION)
                ->nullable()
                ->comment('Date de validation (scan QR)');

            $table->timestampTz(BilletSchema::DATE_UTILISATION)
                ->nullable()
                ->comment('Date d\'utilisation (entrée en salle)');

            // Validation et contrôle d'accès
            $table->string(BilletSchema::CODE_VALIDATION, 20)
                ->unique()
                ->comment('Code court pour validation manuelle');

            $table->string(BilletSchema::EMPLOYE_VALIDATION, 100)
                ->nullable()
                ->comment('Employé qui a validé le billet');

            $table->string(BilletSchema::TERMINAL_VALIDATION, 50)
                ->nullable()
                ->comment('Terminal/device de validation');

            // Métadonnées de livraison
            $table->enum(BilletSchema::MODE_LIVRAISON, [
                'EMAIL',
                'SMS',
                'MOBILE_APP',
                'GUICHET',
                'BORNE',
            ])->default('EMAIL');

            $table->string(BilletSchema::ADRESSE_LIVRAISON, 320)
                ->nullable()
                ->comment('Email ou téléphone de livraison');

            $table->timestampTz(BilletSchema::DATE_DERNIERE_TENTATIVE_ENVOI)
                ->nullable();

            $table->integer(BilletSchema::NOMBRE_TENTATIVES_ENVOI)
                ->default(0);

            // Informations techniques QR
            $table->enum(BilletSchema::FORMAT_QR, [
                'PNG',
                'SVG',
                'PDF',
            ])->default('PNG');

            $table->integer(BilletSchema::TAILLE_QR_PIXELS)
                ->default(200);

            $table->string(BilletSchema::NIVEAU_CORRECTION_QR, 1)
                ->default('M')
                ->comment('L, M, Q, H');

            // Sécurité et anti-fraude
            $table->string(BilletSchema::SIGNATURE_NUMERIQUE, 128)
                ->comment('Signature pour vérification d\'authenticité');

            $table->timestampTz(BilletSchema::DATE_EXPIRATION_QR)
                ->comment('Date d\'expiration du QR code');

            $table->integer(BilletSchema::NOMBRE_SCANS)
                ->default(0)
                ->comment('Nombre de fois que le QR a été scanné');

            $table->jsonb(BilletSchema::HISTORIQUE_SCANS)
                ->nullable()
                ->comment('Historique des scans (timestamps, devices)');

            // Métadonnées additionnelles
            $table->jsonb(BilletSchema::METADONNEES_IMPRESSION)
                ->nullable()
                ->comment('Info PDF, format, template utilisé');

            $table->text(BilletSchema::NOTES)
                ->nullable();

            $table->timestampsTz();

            // FK vers réservation
            $table->foreign(BilletSchema::RESERVATION_KEY)
                ->references(ReservationSchema::PRIMARY_KEY)
                ->on(ReservationSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(BilletSchema::RESERVATION_ID)
                ->references(ReservationSchema::ID)
                ->on(ReservationSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour recherche UUID business
            $table->index(BilletSchema::ID, BilletSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(BilletSchema::NUMERO_BILLET, BilletSchema::INDEX_NUMERO);
            $table->index(BilletSchema::RESERVATION_KEY, BilletSchema::INDEX_RESERVATION);
            $table->index(BilletSchema::QR_CODE_HASH, BilletSchema::INDEX_QR_HASH);
            $table->index(BilletSchema::STATUT, BilletSchema::INDEX_STATUT);
            $table->index(BilletSchema::CODE_VALIDATION, BilletSchema::INDEX_CODE_VALIDATION);
            $table->index(BilletSchema::DATE_EMISSION, BilletSchema::INDEX_DATE_EMISSION);
            $table->index(BilletSchema::DATE_VALIDATION, BilletSchema::INDEX_DATE_VALIDATION);

            // Index composés pour recherche avancée
            $table->index([BilletSchema::RESERVATION_KEY, BilletSchema::STATUT], BilletSchema::INDEX_RESERVATION_STATUT);
            $table->index([BilletSchema::RANGEE, BilletSchema::NUMERO_PLACE], BilletSchema::INDEX_PLACE);
            $table->index([BilletSchema::DATE_EMISSION, BilletSchema::STATUT], BilletSchema::INDEX_DATE_STATUT);

            // Contrainte unique place par réservation
            $table->unique([
                BilletSchema::RESERVATION_KEY,
                BilletSchema::RANGEE,
                BilletSchema::NUMERO_PLACE,
            ], BilletSchema::UNIQUE_PLACE_RESERVATION);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries historique et métadonnées
        $helper->addGinIndex(
            BilletSchema::FULL_TABLE,
            BilletSchema::HISTORIQUE_SCANS,
            BilletSchema::INDEX_HISTORIQUE_SCANS
        );

        $helper->addGinIndex(
            BilletSchema::FULL_TABLE,
            BilletSchema::METADONNEES_IMPRESSION,
            BilletSchema::INDEX_METADONNEES_IMPRESSION
        );

        // Commentaire sur la table
        $helper->addTableComment(
            BilletSchema::FULL_TABLE,
            'Billets individuels avec QR codes pour US14 et validation mobile'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(BilletSchema::FULL_TABLE);
    }
};
