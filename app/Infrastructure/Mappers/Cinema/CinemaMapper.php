<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Cinema;

use Exception;
use App\Domain\Shared\Enums\CodePays;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use App\Domain\Cinema\Entities\Cinema as CinemaEntity;
use App\Infrastructure\Database\Models\Cinema\Cinema as CinemaModel;

final class CinemaMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(CinemaEntity $entity): CinemaModel
    {
        $model = new CinemaModel;

        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(CinemaModel $model): CinemaEntity
    {
        // Gérer les adresses corrompues par l'ancienne validation faible
        $adresse = $model->adresse;
        if ($adresse === null) {
            // Créer une adresse temporaire à partir des données brutes en base
            $rawData = $model->getAttributes();
            if (isset($rawData['adresse'])) {
                $decodedData = json_decode($rawData['adresse'], true);
                if ($decodedData) {
                    // Inverser ville/code_postal si nécessaire pour compatibilité
                    $ville      = $decodedData['ville'] ?? '';
                    $codePostal = $decodedData['code_postal'] ?? '';

                    // Détecter inversion : si ville contient que des chiffres, inverser
                    if (is_numeric($ville) && !is_numeric($codePostal)) {
                        [$ville, $codePostal] = [$codePostal, $ville];
                    }

                    try {
                        $adresse = new Address(
                            rue: $decodedData['rue'] ?? 'Adresse temporaire',
                            ville: $ville ?: 'Ville temporaire',
                            codePostal: $codePostal ?: '00000',
                            pays: $decodedData['pays'] ?? 'FR'
                        );
                    } catch (Exception) {
                        // Fallback si même la correction échoue
                        $adresse = new Address(
                            rue: 'Adresse temporaire',
                            ville: 'Ville temporaire',
                            codePostal: '00000',
                            pays: 'FR'
                        );
                    }
                } else {
                    // Fallback complet
                    $adresse = new Address(
                        rue: 'Adresse temporaire',
                        ville: 'Ville temporaire',
                        codePostal: '00000',
                        pays: 'FR'
                    );
                }
            }
        }

        return new CinemaEntity(
            id: $model->uuid, // UUID business - cast to CinemaId by Eloquent
            nom: $model->nom,
            adresse: $adresse,
            pays: $model->pays,
            telephone: $model->telephone,
            email: $model->email,
            estActif: $model->est_actif,
            description: $model->description,
            coordonneesGps: $model->coordonnees_gps,
            horairesOuverture: $model->horaires_ouverture,
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(CinemaModel $model, CinemaEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Merge Entity data into existing Model (by reference for updates)
     */
    public static function mergeToModel(CinemaEntity $entity, CinemaModel &$model): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): CinemaEntity
    {
        // Validation des champs requis
        if (!isset($data['nom']) || !isset($data['adresse']) || !isset($data['pays'])) {
            throw new Exception('Données requises manquantes pour créer un Cinéma');
        }

        return new CinemaEntity(
            id: isset($data['id']) ? new CinemaId($data['id']) : CinemaId::generate(),
            nom: $data['nom'],
            adresse: Address::fromArray($data['adresse']),
            pays: CodePays::fromCode($data['pays']),
            telephone: PhoneNumber::tryFromInternationalFormat($data['telephone'] ?? null),
            email: Email::tryFromString($data['email'] ?? null),
            estActif: $data['est_actif'] ?? true,
            description: $data['description'] ?? null,
            coordonneesGps: isset($data['coordonnees_gps']) ? CoordonneesGps::tryFromArray($data['coordonnees_gps']) : null,
            horairesOuverture: isset($data['horaires_ouverture']) ? HorairesOuverture::tryFromArray($data['horaires_ouverture']) : null,
        );
    }

    /**
     * Create Domain Entity from array data (safe version)
     *
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?CinemaEntity
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
    public static function toArray(CinemaEntity $entity): array
    {
        return [
            'id'                 => $entity->id->value,
            'nom'                => $entity->nom,
            'adresse'            => $entity->adresse->toArray(),
            'pays'               => $entity->pays->value,
            'telephone'          => $entity->telephone?->telephoneE164,
            'email'              => $entity->email?->toString(),
            'est_actif'          => $entity->estActif,
            'description'        => $entity->description,
            'coordonnees_gps'    => $entity->coordonneesGps?->toArray(),
            'horaires_ouverture' => $entity->horairesOuverture?->toArray(),
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(CinemaEntity $entity, CinemaModel &$model): void
    {
        $model->uuid               = $entity->id;
        $model->nom                = $entity->nom;
        $model->adresse            = $entity->adresse;
        $model->pays               = $entity->pays;
        $model->telephone          = $entity->telephone;
        $model->email              = $entity->email;
        $model->est_actif          = $entity->estActif;
        $model->description        = $entity->description;
        $model->coordonnees_gps    = $entity->coordonneesGps;
        $model->horaires_ouverture = $entity->horairesOuverture;
    }
}
