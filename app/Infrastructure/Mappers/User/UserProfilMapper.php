<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\User;

use DateTime;
use Exception;
use App\Domain\Shared\ValueObjects\Nom;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\Prenom;
use App\Domain\User\ValueObjects\UserProfilId;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\User\Entities\UserProfil as UserProfilEntity;
use App\Infrastructure\Database\Models\Profiles\UserProfil as UserProfilModel;

final class UserProfilMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(UserProfilEntity $entity): UserProfilModel
    {
        $model = new UserProfilModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(UserProfilModel $model): UserProfilEntity
    {
        return new UserProfilEntity(
            id: UserProfilId::fromString($model->uuid),
            userId: UserId::fromString($model->user_uuid),
            prenom: new Prenom($model->prenom),
            nom: new Nom($model->nom),
            dateNaissance: $model->date_naissance,
            telephone: $model->telephone ? new PhoneNumber($model->telephone) : null,
            adresse: $model->adresse,
            preferences: $model->preferences,
            newsletter: $model->newsletter,
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(UserProfilModel $model, UserProfilEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): UserProfilEntity
    {
        if (!isset($data['user_id'], $data['prenom'], $data['nom'])) {
            throw new Exception('Données requises manquantes pour créer un UserProfil');
        }

        return new UserProfilEntity(
            id: isset($data['id']) ? UserProfilId::fromString($data['id']) : UserProfilId::generate(),
            userId: UserId::fromString($data['user_id']),
            prenom: Prenom::fromString($data['prenom']),
            nom: Nom::fromString($data['nom']),
            dateNaissance: isset($data['date_naissance']) ? new DateTime($data['date_naissance']) : null,
            telephone: PhoneNumber::tryFromInternationalFormat($data['telephone'] ?? null),
            adresse: $data['adresse'] ?? null,
            preferences: $data['preferences'] ?? null,
            newsletter: $data['newsletter'] ?? false,
        );
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(UserProfilEntity $entity): array
    {
        return [
            'id'             => $entity->id->value,
            'user_id'        => $entity->userId->value,
            'prenom'         => $entity->prenom->toString(),
            'nom'            => $entity->nom->toString(),
            'date_naissance' => $entity->dateNaissance?->format('Y-m-d'),
            'telephone'      => $entity->telephone?->telephoneE164,
            'adresse'        => $entity->adresse,
            'preferences'    => $entity->preferences,
            'newsletter'     => $entity->newsletter,
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(UserProfilEntity $entity, UserProfilModel &$model): void
    {
        $model->uuid           = $entity->id->value;
        $model->user_uuid      = $entity->userId->value;
        $model->prenom         = $entity->prenom;
        $model->nom            = $entity->nom;
        $model->date_naissance = $entity->dateNaissance;
        $model->telephone      = $entity->telephone;
        $model->adresse        = $entity->adresse;
        $model->preferences    = $entity->preferences;
        $model->newsletter     = $entity->newsletter;
    }
}
