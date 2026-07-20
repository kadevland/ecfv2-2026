<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Cinema;

use DateTime;
use Exception;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\Tarification;
use App\Domain\Cinema\Entities\Seance as SeanceEntity;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Models\Cinema\Seance as SeanceModel;

final class SeanceMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(SeanceEntity $entity): SeanceModel
    {
        $model = new SeanceModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(SeanceModel $model): SeanceEntity
    {
        return new SeanceEntity(
            id: SeanceId::fromString($model->{SeanceSchema::ID}),
            // @phpstan-ignore property.notFound
            filmId: FilmId::fromString($model->{SeanceSchema::FILM_ID}),
            // @phpstan-ignore property.notFound
            salleId: SalleId::fromString($model->{SeanceSchema::SALLE_ID}),
            dateHeureDebut: $model->{SeanceSchema::DATE_HEURE_DEBUT},
            dateHeureFin: $model->{SeanceSchema::DATE_HEURE_FIN},
            version: $model->{SeanceSchema::VERSION},
            tarification: $model->{SeanceSchema::TARIFICATION}, // Cast automatique via AsTarification
            tauxTva: $model->{SeanceSchema::TAUX_TVA}, // Cast automatique via AsTauxTva
            devise: $model->{SeanceSchema::DEVISE}, // Cast automatique via AsDevise
            placementLibre: $model->{SeanceSchema::PLACEMENT_LIBRE},
            // @phpstan-ignore argument.type
            statut: $model->{SeanceSchema::STATUT},
            // @phpstan-ignore property.notFound
            dureeAdditionnelle: $model->{SeanceSchema::DUREE_ADDITIONNELLE},
            // @phpstan-ignore property.notFound
            qualiteProjection: $model->{SeanceSchema::QUALITE_PROJECTION},
            // @phpstan-ignore property.notFound
            qualiteSonore: $model->{SeanceSchema::QUALITE_SONORE},
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(SeanceModel $model, SeanceEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): SeanceEntity
    {
        if (!isset($data['film_uuid'], $data['salle_uuid'], $data['date_heure_debut'], $data['date_heure_fin'], $data['version'], $data['tarification'], $data['taux_tva'], $data['devise'])) {
            throw new Exception('Données requises manquantes pour créer une Seance');
        }

        return new SeanceEntity(
            id: isset($data['id']) ? SeanceId::fromString($data['id']) : SeanceId::generate(),
            filmId: FilmId::fromString($data['film_uuid']),
            salleId: SalleId::fromString($data['salle_uuid']),
            dateHeureDebut: new DateTime($data['date_heure_debut']),
            dateHeureFin: new DateTime($data['date_heure_fin']),
            version: $data['version'],
            tarification: is_array($data['tarification']) ? Tarification::fromArray($data['tarification']) : $data['tarification'],
            tauxTva: is_array($data['taux_tva']) ? TauxTva::fromArray($data['taux_tva']) : $data['taux_tva'],
            // @phpstan-ignore staticMethod.notFound
            devise: is_array($data['devise']) ? Devise::fromArray($data['devise']) : $data['devise'],
            placementLibre: $data['placement_libre'] ?? false,
            statut: $data['statut'] ?? 'programmee',
            dureeAdditionnelle: $data['duree_additionnelle'] ?? null,
            qualiteProjection: $data['qualite_projection'] ?? null,
            qualiteSonore: $data['qualite_sonore'] ?? null,
        );
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(SeanceEntity $entity): array
    {
        return [
            'id'               => $entity->id->value,
            'film_id'          => $entity->filmId->value,
            'salle_id'         => $entity->salleId->value,
            'date_heure_debut' => $entity->dateHeureDebut->format('Y-m-d H:i:s'),
            'date_heure_fin'   => $entity->dateHeureFin->format('Y-m-d H:i:s'),
            'version'          => $entity->version,
            'tarification'     => $entity->tarification->toArray(),
            'taux_tva'         => $entity->tauxTva->toArray(),
            // @phpstan-ignore method.notFound
            'devise'              => $entity->devise->toArray(),
            'placement_libre'     => $entity->placementLibre,
            'statut'              => $entity->statut,
            'duree_additionnelle' => $entity->dureeAdditionnelle,
            'qualite_projection'  => $entity->qualiteProjection,
            'qualite_sonore'      => $entity->qualiteSonore,
        ];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(SeanceEntity $entity, SeanceModel &$model): void
    {
        // New domain model columns using Schema constants
        $model->{SeanceSchema::ID} = $entity->id->value;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::FILM_ID} = $entity->filmId->value;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::SALLE_ID} = $entity->salleId->value;
        // @phpstan-ignore assign.propertyType
        $model->{SeanceSchema::DATE_HEURE_DEBUT} = $entity->dateHeureDebut;
        // @phpstan-ignore assign.propertyType
        $model->{SeanceSchema::DATE_HEURE_FIN}  = $entity->dateHeureFin;
        $model->{SeanceSchema::VERSION}         = strtoupper($entity->version);
        $model->{SeanceSchema::TARIFICATION}    = $entity->tarification;
        $model->{SeanceSchema::TAUX_TVA}        = $entity->tauxTva;
        $model->{SeanceSchema::DEVISE}          = $entity->devise;
        $model->{SeanceSchema::PLACEMENT_LIBRE} = $entity->placementLibre;
        // @phpstan-ignore argument.type
        $model->{SeanceSchema::STATUT} = strtoupper($entity->statut->value);
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::DUREE_ADDITIONNELLE} = $entity->dureeAdditionnelle;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::QUALITE_PROJECTION} = $entity->qualiteProjection;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::QUALITE_SONORE} = $entity->qualiteSonore;

        // Legacy table columns for backward compatibility using Schema constants
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::DATE_SEANCE} = $entity->dateHeureDebut->format('Y-m-d');
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::HEURE_DEBUT} = $entity->dateHeureDebut->format('H:i:s');
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::HEURE_FIN} = $entity->dateHeureFin->format('H:i:s');
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::EST_3D} = false; // Default value
        $prixNormal                    = $entity->tarification->getPrixNormal();
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::PRIX_HT_CENTIMES} = $prixNormal ? $prixNormal->getAmount() : 0;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::TAUX_TVA_BASIS_POINTS} = $entity->tauxTva->getBasisPoints();
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::PRIX_TTC_CENTIMES} = $prixNormal ? (int) ($prixNormal->getAmount() * (1 + $entity->tauxTva->getPercentage() / 100)) : 0;
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::PLACES_DISPONIBLES_OLD} = 0; // Default value - will be calculated later
        // @phpstan-ignore property.notFound
        $model->{SeanceSchema::SEANCE_SPECIALE_OLD} = false; // Default value

        // Populate foreign key db_ids using Schema constants
        // @phpstan-ignore property.notFound
        if (!$model->{SeanceSchema::FILM_KEY}) {
            $film                            = \App\Infrastructure\Database\Models\Cinema\Film::where('uuid', $entity->filmId->value)->first();
            $model->{SeanceSchema::FILM_KEY} = $film?->db_id;
        }

        // @phpstan-ignore property.notFound
        if (!$model->{SeanceSchema::SALLE_KEY}) {
            $salle                            = \App\Infrastructure\Database\Models\Cinema\Salle::where('uuid', $entity->salleId->value)->first();
            $model->{SeanceSchema::SALLE_KEY} = $salle?->db_id;
        }
    }
}
