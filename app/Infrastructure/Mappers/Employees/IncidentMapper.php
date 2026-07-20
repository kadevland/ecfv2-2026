<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Employees;

use DateTime;
use Exception;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Domain\Employees\ValueObjects\IncidentId;
use App\Domain\Employees\Entities\Incident as IncidentEntity;
use App\Infrastructure\Database\Models\Employees\Incident as IncidentModel;

final class IncidentMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(IncidentEntity $entity): IncidentModel
    {
        $model = new IncidentModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(IncidentModel $model): IncidentEntity
    {
        return new IncidentEntity(
            id: IncidentId::fromString($model->id),
            emploiDeclarantId: EmploiId::fromString($model->employee_id),
            cinemaId: CinemaId::fromString($model->cinema_id),
            typeIncident: $model->type_incident,
            severite: $model->severite,
            titre: $model->titre,
            description: $model->description,
            salleId: $model->salle_id ? SalleId::fromString($model->salle_id) : null,
            statut: $model->statut,
            dateResolution: $model->date_resolution,
            responsableResolution: $model->responsable_resolution,
            piecesJointes: $model->pieces_jointes,
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(IncidentModel $model, IncidentEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): IncidentEntity
    {
        if (!isset($data['emploi_declarant_id'], $data['cinema_id'], $data['type_incident'], $data['severite'], $data['titre'], $data['description'])) {
            throw new Exception('Données requises manquantes pour créer un Incident');
        }

        return new IncidentEntity(
            id: isset($data['id']) ? IncidentId::fromString($data['id']) : IncidentId::generate(),
            emploiDeclarantId: EmploiId::fromString($data['emploi_declarant_id']),
            cinemaId: CinemaId::fromString($data['cinema_id']),
            typeIncident: $data['type_incident'],
            severite: $data['severite'],
            titre: $data['titre'],
            description: $data['description'],
            salleId: isset($data['salle_id']) ? SalleId::fromString($data['salle_id']) : null,
            statut: $data['statut'] ?? 'ouvert',
            dateResolution: isset($data['date_resolution']) ? new DateTime($data['date_resolution']) : null,
            responsableResolution: $data['responsable_resolution'] ?? null,
            piecesJointes: $data['pieces_jointes'] ?? null,
        );
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(IncidentEntity $entity): array
    {
        return [
            'id'                     => $entity->id->value,
            'emploi_declarant_id'    => $entity->emploiDeclarantId->value,
            'cinema_id'              => $entity->cinemaId->value,
            'salle_id'               => $entity->salleId?->value,
            'type_incident'          => $entity->typeIncident,
            'severite'               => $entity->severite,
            'titre'                  => $entity->titre,
            'description'            => $entity->description,
            'statut'                 => $entity->statut,
            'date_resolution'        => $entity->dateResolution?->format('Y-m-d H:i:s'),
            'responsable_resolution' => $entity->responsableResolution,
            'pieces_jointes'         => $entity->piecesJointes,
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(IncidentEntity $entity, IncidentModel &$model): void
    {
        $model->id                     = $entity->id->value;
        $model->employee_id            = $entity->emploiDeclarantId->value;
        $model->cinema_id              = $entity->cinemaId->value;
        $model->salle_id               = $entity->salleId?->value;
        $model->type_incident          = $entity->typeIncident;
        $model->severite               = $entity->severite;
        $model->titre                  = $entity->titre;
        $model->description            = $entity->description;
        $model->statut                 = $entity->statut;
        $model->date_resolution        = $entity->dateResolution;
        $model->responsable_resolution = $entity->responsableResolution;
        $model->pieces_jointes         = $entity->piecesJointes;
    }
}
