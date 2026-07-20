<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Reservations\PaiementSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

return new class extends Migration
{
    protected $connection = PaiementSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le schéma reservations s'il n'existe pas
        $helper = new SchemaHelper(DB::connection(PaiementSchema::CONNECTION));
        $helper->createSchemaIfNotExists(PaiementSchema::SCHEMA);

        Schema::create(PaiementSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(PaiementSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(PaiementSchema::ID)
                ->unique();

            // Référence vers la réservation
            $table->bigInteger(PaiementSchema::RESERVATION_KEY)->unsigned();
            $table->uuid(PaiementSchema::RESERVATION_ID);

            // Numéro de transaction unique
            $table->string(PaiementSchema::NUMERO_TRANSACTION, 50)
                ->unique()
                ->comment('Numéro unique de transaction');

            // Montants MoneyPHP
            $table->integer(PaiementSchema::MONTANT_HT_CENTIMES)
                ->comment('Montant HT en centimes');

            $table->string(PaiementSchema::DEVISE, 3)
                ->default('EUR');

            $table->integer(PaiementSchema::TAUX_TVA_BASIS_POINTS)
                ->comment('Taux TVA en basis points');

            $table->integer(PaiementSchema::MONTANT_TTC_CENTIMES)
                ->comment('Montant TTC en centimes');

            // Méthode et statut de paiement
            $table->enum(PaiementSchema::METHODE_PAIEMENT, [
                'CARTE_BANCAIRE',
                'PAYPAL',
                'VIREMENT',
                'ESPECES',
                'CHEQUE',
                'CARTE_CADEAU',
                'CREDIT_COMPTE',
            ]);

            $table->enum(PaiementSchema::STATUT, [
                'EN_ATTENTE',
                'EN_COURS',
                'AUTORISE',
                'CAPTURE',
                'REUSSI',
                'ECHEC',
                'ANNULE',
                'REMBOURSE',
                'REMBOURSE_PARTIEL',
                'EXPIRE',
            ])->default('EN_ATTENTE');

            // Informations de paiement
            $table->string(PaiementSchema::REFERENCE_EXTERNE, 100)
                ->nullable()
                ->comment('ID transaction du PSP (Stripe, PayPal, etc.)');

            $table->string(PaiementSchema::PROCESSEUR_PAIEMENT, 50)
                ->nullable()
                ->comment('STRIPE, PAYPAL, LYRA, etc.');

            // Dates importantes
            $table->timestampTz(PaiementSchema::DATE_PAIEMENT);

            $table->timestampTz(PaiementSchema::DATE_AUTORISATION)
                ->nullable();

            $table->timestampTz(PaiementSchema::DATE_CAPTURE)
                ->nullable();

            $table->timestampTz(PaiementSchema::DATE_REMBOURSEMENT)
                ->nullable();

            // Informations carte (sécurisées/tokenisées)
            $table->string(PaiementSchema::CARTE_MASQUEE, 20)
                ->nullable()
                ->comment('Numéro masqué (ex: ****1234)');

            $table->string(PaiementSchema::CARTE_TYPE, 20)
                ->nullable()
                ->comment('VISA, MASTERCARD, AMEX');

            $table->string(PaiementSchema::CARTE_TOKEN, 100)
                ->nullable()
                ->comment('Token sécurisé pour remboursement');

            // Codes de réponse
            $table->string(PaiementSchema::CODE_AUTORISATION, 20)
                ->nullable();

            $table->string(PaiementSchema::CODE_REPONSE, 10)
                ->nullable();

            $table->text(PaiementSchema::MESSAGE_REPONSE)
                ->nullable();

            // Sécurité et fraude
            $table->decimal(PaiementSchema::SCORE_FRAUDE, 5, 2)
                ->nullable()
                ->comment('Score de risque fraude (0-100)');

            $table->enum(PaiementSchema::RESULTAT_3DS, [
                'NON_APPLICABLE',
                'AUTHENTIFIE',
                'CHALLENGE',
                'ECHEC',
                'ERREUR',
            ])->nullable();

            $table->string(PaiementSchema::REFERENCE_3DS, 100)
                ->nullable();

            // Remboursements
            $table->integer(PaiementSchema::MONTANT_REMBOURSE_CENTIMES)
                ->default(0)
                ->comment('Montant total remboursé');

            $table->text(PaiementSchema::RAISON_REMBOURSEMENT)
                ->nullable();

            // Informations techniques
            $table->ipAddress(PaiementSchema::IP_PAIEMENT)
                ->nullable();

            $table->string(PaiementSchema::USER_AGENT, 500)
                ->nullable();

            $table->jsonb(PaiementSchema::DONNEES_PSP)
                ->nullable()
                ->comment('Données brutes du processeur de paiement');

            $table->jsonb(PaiementSchema::METADONNEES_PAIEMENT)
                ->nullable()
                ->comment('Métadonnées additionnelles');

            // Gestion des erreurs
            $table->integer(PaiementSchema::NOMBRE_TENTATIVES)
                ->default(1);

            $table->timestampTz(PaiementSchema::DATE_DERNIERE_TENTATIVE)
                ->nullable();

            $table->text(PaiementSchema::HISTORIQUE_ERREURS)
                ->nullable();

            // Notes et commentaires
            $table->text(PaiementSchema::NOTES_INTERNES)
                ->nullable();

            $table->timestampsTz();

            // FK vers réservation
            $table->foreign(PaiementSchema::RESERVATION_KEY)
                ->references(ReservationSchema::PRIMARY_KEY)
                ->on(ReservationSchema::FULL_TABLE)
                ->onDelete('restrict');

            $table->foreign(PaiementSchema::RESERVATION_ID)
                ->references(ReservationSchema::ID)
                ->on(ReservationSchema::FULL_TABLE)
                ->onDelete('restrict');

            // Index pour recherche UUID business
            $table->index(PaiementSchema::ID, PaiementSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index(PaiementSchema::NUMERO_TRANSACTION, PaiementSchema::INDEX_NUMERO_TRANSACTION);
            $table->index(PaiementSchema::RESERVATION_KEY, PaiementSchema::INDEX_RESERVATION);
            $table->index(PaiementSchema::REFERENCE_EXTERNE, PaiementSchema::INDEX_REFERENCE_EXTERNE);
            $table->index(PaiementSchema::STATUT, PaiementSchema::INDEX_STATUT);
            $table->index(PaiementSchema::METHODE_PAIEMENT, PaiementSchema::INDEX_METHODE);
            $table->index(PaiementSchema::DATE_PAIEMENT, PaiementSchema::INDEX_DATE_PAIEMENT);
            $table->index(PaiementSchema::PROCESSEUR_PAIEMENT, PaiementSchema::INDEX_PROCESSEUR);

            // Index composés pour recherche avancée
            $table->index([PaiementSchema::RESERVATION_KEY, PaiementSchema::STATUT], PaiementSchema::INDEX_RESERVATION_STATUT);
            $table->index([PaiementSchema::DATE_PAIEMENT, PaiementSchema::STATUT], PaiementSchema::INDEX_DATE_STATUT);
            $table->index([PaiementSchema::METHODE_PAIEMENT, PaiementSchema::STATUT], PaiementSchema::INDEX_METHODE_STATUT);

            // Note: Contraintes métier gérées au niveau applicatif pour simplifier
            // Laravel Blueprint::check() n'existe pas nativement
        });

        // Index GIN sur JSONB pour queries données PSP et métadonnées
        $helper->addGinIndex(
            PaiementSchema::FULL_TABLE,
            PaiementSchema::DONNEES_PSP,
            PaiementSchema::INDEX_DONNEES_PSP
        );

        $helper->addGinIndex(
            PaiementSchema::FULL_TABLE,
            PaiementSchema::METADONNEES_PAIEMENT,
            PaiementSchema::INDEX_METADONNEES_PAIEMENT
        );

        // Commentaire sur la table
        $helper->addTableComment(
            PaiementSchema::FULL_TABLE,
            'Paiements avec support MoneyPHP et intégration PSP complète'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(PaiementSchema::FULL_TABLE);
    }
};
