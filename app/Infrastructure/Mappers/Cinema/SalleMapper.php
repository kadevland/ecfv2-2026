<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Cinema;

use Exception;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Application\Salle\DTOs\SalleEditDto;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Salle\DTOs\SalleDetailDto;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\Entities\Salle as SalleEntity;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;

final class SalleMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(SalleEntity $entity): SalleModel
    {
        $model = new SalleModel;

        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(SalleModel $model): SalleEntity
    {
        // Convert quality arrays from strings to enums
        $qualiteProjection = array_map(
            fn ($value) => QualiteProjection::from($value),
            $model->qualite_projection ?? []
        );

        $qualiteSonore = array_map(
            fn ($value) => QualiteSonore::from($value),
            $model->qualite_sonore ?? []
        );

        return new SalleEntity(
            id: new SalleId($model->uuid),
            cinemaId: new CinemaId($model->cinema_uuid),
            nom: $model->nom,
            capaciteTotale: $model->capacite_totale,
            nombreRangees: $model->nombre_rangees,
            placesParRangee: $model->places_par_rangee,
            placesStandard: $model->places_standard,
            placesPmr: $model->places_pmr,
            qualiteProjection: $qualiteProjection,
            qualiteSonore: $qualiteSonore,
            accessibilitePmr: $model->accessibilite_pmr ?? false,
            climatisation: $model->climatisation ?? true,
            planSalle: $model->plan_salle,
            statut: StatutSalle::from($model->statut),
        );
    }

    /**
     * Convert Eloquent Model to SalleDetailDto (with cinema details)
     */
    public static function toDetailDto(SalleModel $model): SalleDetailDto
    {
        return new SalleDetailDto(
            uuid: $model->uuid,
            nom: $model->nom,
            capaciteTotale: $model->capacite_totale,
            nombreRangees: $model->nombre_rangees,
            placesParRangee: $model->places_par_rangee,
            placesStandard: $model->places_standard,
            placesPmr: $model->places_pmr,
            qualiteProjection: $model->qualite_projection ?? [],
            qualiteSonore: $model->qualite_sonore ?? [],
            climatisation: $model->climatisation,
            accessibilitePmr: $model->accessibilite_pmr,
            planSalle: $model->plan_salle,
            statut: $model->statut,
            cinemaUuid: $model->cinema_uuid,
            cinemaDbId: $model->cinema_db_id,
            cinemaNom: $model->cinema?->nom ?? '',
            cinemaVille: $model->cinema?->ville ?? '',
        );
    }

    /**
     * Convert Eloquent Model to SalleEditDto (with cinema details for editing)
     */
    public static function toEditDto(SalleModel $model): SalleEditDto
    {
        return new SalleEditDto(
            uuid: $model->uuid,
            nom: $model->nom,
            capaciteTotale: $model->capacite_totale,
            nombreRangees: $model->nombre_rangees,
            placesParRangee: $model->places_par_rangee,
            placesStandard: $model->places_standard,
            placesPmr: $model->places_pmr,
            qualiteProjection: $model->qualite_projection ?? [],
            qualiteSonore: $model->qualite_sonore ?? [],
            climatisation: $model->climatisation,
            accessibilitePmr: $model->accessibilite_pmr,
            planSalle: $model->plan_salle,
            statut: $model->statut,
            cinemaUuid: $model->cinema_uuid,
            cinemaNom: $model->cinema?->nom ?? '',
            cinemaVille: $model->cinema?->ville ?? '',
            estDisponible: $model->statut === 'ACTIVE',
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(SalleModel $model, SalleEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Merge Entity data into existing Model (by reference for updates)
     */
    public static function mergeToModel(SalleEntity $entity, SalleModel &$model): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): SalleEntity
    {
        // Validation des champs requis
        if (!isset($data['cinema_uuid']) || !isset($data['nom']) || !isset($data['capacite_totale'])) {
            throw new Exception('Données requises manquantes pour créer une Salle');
        }

        // Convert quality arrays from strings to enums
        $qualiteProjection = array_map(
            fn ($value) => QualiteProjection::from($value),
            $data['qualite_projection'] ?? []
        );

        $qualiteSonore = array_map(
            fn ($value) => QualiteSonore::from($value),
            $data['qualite_sonore'] ?? []
        );

        return new SalleEntity(
            id: isset($data['id']) ? new SalleId($data['id']) : SalleId::generate(),
            cinemaId: new CinemaId($data['cinema_uuid']),
            nom: $data['nom'],
            capaciteTotale: (int) $data['capacite_totale'],
            nombreRangees: (int) ($data['nombre_rangees'] ?? 1),
            placesParRangee: (int) ($data['places_par_rangee'] ?? 1),
            placesStandard: (int) ($data['places_standard'] ?? 0),
            placesPmr: (int) ($data['places_pmr'] ?? 0),
            qualiteProjection: $qualiteProjection,
            qualiteSonore: $qualiteSonore,
            accessibilitePmr: (bool) ($data['accessibilite_pmr'] ?? false),
            climatisation: (bool) ($data['climatisation'] ?? true),
            planSalle: $data['plan_salle'] ?? null,
            statut: StatutSalle::from($data['statut'] ?? 'ACTIVE'),
        );
    }

    /**
     * Create Domain Entity from array data (safe version)
     *
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?SalleEntity
    {
        if ($data === null || empty($data)) {
            return null;
        }

        try {
            return self::fromArray($data);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(SalleEntity $entity): array
    {
        return [
            'id'                 => $entity->id->value,
            'cinema_uuid'        => $entity->cinemaId->value,
            'nom'                => $entity->nom,
            'capacite_totale'    => $entity->capaciteTotale,
            'nombre_rangees'     => $entity->nombreRangees,
            'places_par_rangee'  => $entity->placesParRangee,
            'places_standard'    => $entity->placesStandard,
            'places_pmr'         => $entity->placesPmr,
            'qualite_projection' => array_map(fn ($q) => $q->value, $entity->qualiteProjection),
            'qualite_sonore'     => array_map(fn ($q) => $q->value, $entity->qualiteSonore),
            'accessibilite_pmr'  => $entity->accessibilitePmr,
            'climatisation'      => $entity->climatisation,
            'plan_salle'         => $entity->planSalle,
            'statut'             => $entity->statut->value,
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(SalleEntity $entity, SalleModel &$model): void
    {
        $model->uuid        = $entity->id->value;
        $model->cinema_uuid = $entity->cinemaId->value;

        $model->nom                = $entity->nom;
        $model->capacite_totale    = $entity->capaciteTotale;
        $model->nombre_rangees     = $entity->nombreRangees;
        $model->places_par_rangee  = $entity->placesParRangee;
        $model->places_standard    = $entity->placesStandard;
        $model->places_pmr         = $entity->placesPmr;
        $model->qualite_projection = array_map(fn ($q) => $q->value, $entity->qualiteProjection);
        $model->qualite_sonore     = array_map(fn ($q) => $q->value, $entity->qualiteSonore);
        $model->accessibilite_pmr  = $entity->accessibilitePmr;
        $model->climatisation      = $entity->climatisation;
        $model->plan_salle         = $entity->planSalle;
        $model->statut             = $entity->statut->value;
    }
}
