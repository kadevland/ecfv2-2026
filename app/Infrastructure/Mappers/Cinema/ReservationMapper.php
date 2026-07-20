<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Cinema;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\ReservationId;
use App\Domain\Cinema\ValueObjects\UtilisateurId;
use App\Domain\Cinema\Entities\Reservation as ReservationEntity;
use App\Infrastructure\Database\Schemas\Cinema\ReservationSchema;
use App\Infrastructure\Database\Models\Cinema\Reservation as ReservationModel;

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
        //dd($model);

        return new ReservationEntity(
            id: ReservationId::fromString($model->{ReservationSchema::ID}),
            seanceId: SeanceId::fromString($model->{ReservationSchema::SEANCE_ID}),
            utilisateurId: UtilisateurId::fromString($model->{ReservationSchema::UTILISATEUR_ID}),
            nombrePlaces: $model->{ReservationSchema::NOMBRE_PLACES},
            montantTotal: new Money(
                //amount: $model->{ReservationSchema::MONTANT_TOTAL},
                amountInCentimes: (int)$model->prix_total_ht_centimes ?? 0,
                devise: $model->{ReservationSchema::DEVISE} ?? Devise::EUR()
            ),
            statut: $model->{ReservationSchema::STATUT},
            dateReservation: $model->{ReservationSchema::DATE_RESERVATION}->toDate(),
            dateExpiration: $model->{ReservationSchema::DATE_EXPIRATION}?->toDate()
        );
    }

    /**
     * Update existing model with entity data
     */
    public static function updateModel(ReservationModel $model, ReservationEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create a new model instance from entity
     */
    public static function createFromEntity(ReservationEntity $entity): ReservationModel
    {
        $model = new ReservationModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Assign entity data to model attributes
     */
    private static function assignEntityDataToModel(ReservationEntity $entity, ReservationModel $model): void
    {
        $model->{ReservationSchema::ID}               = $entity->id->value;
        $model->{ReservationSchema::SEANCE_ID}        = $entity->seanceId->value;
        $model->{ReservationSchema::UTILISATEUR_ID}   = $entity->utilisateurId->value;
        $model->{ReservationSchema::NOMBRE_PLACES}    = $entity->nombrePlaces;
        $model->{ReservationSchema::MONTANT_TOTAL}    = $entity->montantTotal->getAmount();
        $model->{ReservationSchema::DEVISE}           = $entity->montantTotal->getDevise();
        $model->{ReservationSchema::STATUT}           = $entity->statut;
        $model->{ReservationSchema::DATE_RESERVATION} = $entity->dateReservation;
        $model->{ReservationSchema::DATE_EXPIRATION}  = $entity->dateExpiration;

        $model->{ReservationSchema::DETAILS_BILLETS}       = [];
        $model->{ReservationSchema::INFORMATIONS_PAIEMENT} = [];
    }
}
