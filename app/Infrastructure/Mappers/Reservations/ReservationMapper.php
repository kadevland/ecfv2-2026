<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Reservations;

use DateTime;
use Exception;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Reservations\Entities\Reservation as ReservationEntity;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;
use App\Infrastructure\Database\Models\Reservations\Reservation as ReservationModel;

final class ReservationMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(ReservationEntity $entity): ReservationModel
    {
        $model = new ReservationModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(ReservationModel $model): ReservationEntity
    {
        return new ReservationEntity(
            id: ReservationId::fromString($model->{ReservationSchema::ID}), // Use UUID column
            numeroReservation: $model->numero_reservation,
            userId: UserId::fromString($model->user_uuid),
            seanceId: SeanceId::fromString($model->{ReservationSchema::SEANCE_ID}), // Use schema constant
            nombrePlaces: $model->nombre_places,
            placesDetails: $model->{ReservationSchema::DETAILS_PLACES} ?? [], // Use schema constant
            montantTotal: \Money\Money::EUR($model->{ReservationSchema::PRIX_TOTAL_TTC_CENTIMES}), // From actual column
            montantHt: \Money\Money::EUR($model->{ReservationSchema::PRIX_TOTAL_HT_CENTIMES}), // From actual column
            tauxTva: \App\Domain\Shared\ValueObjects\TauxTva::fromBasisPoints($model->{ReservationSchema::TAUX_TVA_BASIS_POINTS}),
            statut: strtolower($model->statut), // Convert back to lowercase
            dateExpiration: $model->{ReservationSchema::DATE_EXPIRATION},
            commentaires: $model->{ReservationSchema::NOTES_CLIENT},
            qrCode: null, // No QR code column in actual table
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(ReservationModel $model, ReservationEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): ReservationEntity
    {
        if (!isset($data['numero_reservation'], $data['user_id'], $data['seance_id'], $data['nombre_places'], $data['places_details'], $data['montant_total'], $data['montant_ht'], $data['taux_tva'])) {
            throw new Exception('Données requises manquantes pour créer une Reservation');
        }

        return new ReservationEntity(
            id: isset($data['id']) ? ReservationId::fromString($data['id']) : ReservationId::generate(),
            numeroReservation: $data['numero_reservation'],
            userId: UserId::fromString($data['user_id']),
            seanceId: SeanceId::fromString($data['seance_id']),
            nombrePlaces: $data['nombre_places'],
            placesDetails: $data['places_details'],
            montantTotal: \Money\Money::EUR($data['montant_total']),
            montantHt: \Money\Money::EUR($data['montant_ht']),
            tauxTva: TauxTva::fromBasisPoints($data['taux_tva']),
            statut: $data['statut'] ?? 'en_attente',
            dateExpiration: isset($data['date_expiration']) ? new DateTime($data['date_expiration']) : null,
            commentaires: $data['commentaires'] ?? null,
            qrCode: $data['qr_code'] ?? null,
        );
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(ReservationEntity $entity): array
    {
        return [
            'id'                 => $entity->id->value,
            'numero_reservation' => $entity->numeroReservation,
            'user_id'            => $entity->userId->value,
            'seance_id'          => $entity->seanceId->value,
            'nombre_places'      => $entity->nombrePlaces,
            'places_details'     => $entity->placesDetails,
            'montant_total'      => $entity->montantTotal->getAmount(),
            'montant_ht'         => $entity->montantHt->getAmount(),
            'taux_tva'           => $entity->tauxTva->getBasisPoints(),
            'statut'             => $entity->statut,
            'date_expiration'    => $entity->dateExpiration?->format('Y-m-d H:i:s'),
            'commentaires'       => $entity->commentaires,
            'qr_code'            => $entity->qrCode,
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(ReservationEntity $entity, ReservationModel &$model): void
    {
        $model->{ReservationSchema::ID} = $entity->id->value; // uuid column
        $model->numero_reservation      = $entity->numeroReservation;

        // Set user references - use UserProfil for FK constraints
        $userProfil = \App\Infrastructure\Database\Models\Profiles\UserProfil::where(\App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::ID, $entity->userId->value)->first();
        if ($userProfil) {
            $model->{ReservationSchema::USER_KEY} = $userProfil->getKey(); // Primary key for FK
            $model->{ReservationSchema::USER_ID}  = $entity->userId->value; // UUID for business logic
        } else {
            throw new Exception('UserProfil not found with ID: ' . $entity->userId->value);
        }

        // Set seance references - need both UUID and primary key for FK constraints
        $seance = \App\Infrastructure\Database\Models\Cinema\Seance::where('uuid', $entity->seanceId->value)->first();
        if ($seance) {
            $model->{ReservationSchema::SEANCE_KEY} = $seance->getKey(); // Primary key for FK
            $model->{ReservationSchema::SEANCE_ID}  = $entity->seanceId->value; // UUID for business logic
        } else {
            throw new Exception('Seance not found with ID: ' . $entity->seanceId->value);
        }
        $model->nombre_places                       = $entity->nombrePlaces;
        $model->{ReservationSchema::DETAILS_PLACES} = $entity->placesDetails; // Use correct column name

        // Map financial data to actual column structure
        $model->{ReservationSchema::PRIX_TOTAL_HT_CENTIMES}  = $entity->montantHt->getAmount();
        $model->{ReservationSchema::PRIX_TOTAL_TTC_CENTIMES} = $entity->montantTotal->getAmount();
        $model->{ReservationSchema::DEVISE}                  = $entity->montantTotal->getCurrency()->getCode();
        $model->{ReservationSchema::TAUX_TVA_BASIS_POINTS}   = $entity->tauxTva->getBasisPoints();

        // Prix unitaire calculé
        $prixUnitaire                                          = $entity->nombrePlaces > 0 ? (int) ($entity->montantHt->getAmount() / $entity->nombrePlaces) : 0;
        $model->{ReservationSchema::PRIX_UNITAIRE_HT_CENTIMES} = $prixUnitaire;

        $model->statut                                  = strtoupper($entity->statut); // Convert to uppercase for DB enum
        $model->{ReservationSchema::DATE_EXPIRATION}    = $entity->dateExpiration;
        $model->{ReservationSchema::DATE_RESERVATION}   = now(); // Set current time
        $model->{ReservationSchema::EMAIL_CONFIRMATION} = 'temp@example.com'; // Temporary
        $model->{ReservationSchema::CODE_CONFIRMATION}  = strtoupper(substr(md5(uniqid()), 0, 8));
        $model->{ReservationSchema::TOKEN_SECURITE}     = hash('sha256', uniqid() . time());

        // Optional fields
        if ($entity->commentaires) {
            $model->{ReservationSchema::NOTES_CLIENT} = $entity->commentaires;
        }
    }
}
