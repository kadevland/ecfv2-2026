<?php

declare(strict_types=1);

namespace App\Infrastructure\Mappers\Cinema;

use DateTime;
use Exception;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Cinema\Entities\Film as FilmEntity;
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;

final class FilmMapper
{
    /**
     * Convert Domain Entity to Eloquent Model
     */
    public static function toModel(FilmEntity $entity): FilmModel
    {
        $model = new FilmModel;
        self::assignEntityDataToModel($entity, $model);

        return $model;
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    public static function toDomain(FilmModel $model): FilmEntity
    {

        // Handle realisateurs - can be string JSON when using select() or array when fully loaded
        $realisateurs = is_string($model->realisateurs)
            ? json_decode($model->realisateurs, true)
            : $model->realisateurs;

        return new FilmEntity(
            id: FilmId::fromString($model->uuid),
            titre: $model->titre,
            realisateurs: $realisateurs ?? [],
            genres: $model->genres ?? [],
            dureeMinutes: $model->duree_minutes,
            classification: $model->classification,
            dateSortie: $model->date_sortie,
            titreOriginal: $model->titre_original,
            acteursPrincipaux: is_array($model->acteurs_principaux) ? $model->acteurs_principaux : self::decodeActeurs($model->acteurs_principaux),
            langueOriginale: $model->langue_originale,
            sousTitres: [],
            synopsis: $model->synopsis,
            paysOrigine: $model->pays_origine,
            producteur: $model->producteur,
            dateFinExploitation: null,
            noteCritique: $model->note_critique ? (float) $model->note_critique : null,
            notePublic: $model->note_public ? (float) $model->note_public : null,
            afficheUrl: $model->affiche_url,
            bandeAnnonceUrl: $model->bande_annonce_url,
            estActif: $model->est_actif,
            statut: $model->statut,
        );
    }

    /**
     * Update existing Model from Domain Entity
     */
    public static function updateModel(FilmModel $model, FilmEntity $entity): void
    {
        self::assignEntityDataToModel($entity, $model);
    }

    /**
     * Create Domain Entity from array data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): FilmEntity
    {
        if (!isset($data['titre'], $data['realisateurs'], $data['genres'], $data['duree_minutes'], $data['classification'], $data['date_sortie'])) {
            throw new Exception('Données requises manquantes pour créer un Film');
        }

        return new FilmEntity(
            id: isset($data['id']) ? FilmId::fromString($data['id']) : FilmId::generate(),
            titre: $data['titre'],
            realisateurs: $data['realisateurs'],
            genres: $data['genres'],
            dureeMinutes: $data['duree_minutes'],
            classification: $data['classification'],
            dateSortie: new DateTime($data['date_sortie']),
            titreOriginal: $data['titre_fr'] ?? null,
            acteursPrincipaux: $data['acteurs_principaux'] ?? [],
            langueOriginale: $data['langue_originale'] ?? null,
            paysOrigine: $data['pays_origine'] ?? null,
            producteur: $data['producteur'] ?? null,
            sousTitres: isset($data['sous_titres']) ? (is_array($data['sous_titres']) ? $data['sous_titres'] : [$data['sous_titres']]) : null,
            synopsis: $data['resume'] ?? null,
            dateFinExploitation: isset($data['date_fin_exploitation']) ? new DateTime($data['date_fin_exploitation']) : null,
            noteCritique: $data['note_presse'] ?? null,
            notePublic: $data['note_public'] ?? null,
            afficheUrl: $data['affiche_url'] ?? null,
            bandeAnnonceUrl: $data['bande_annonce_url'] ?? null,
            estActif: $data['est_actif'] ?? true,
            statut: $data['statut'] ?? null,
        );
    }

    /**
     * Convert Domain Entity to array
     *
     * @return array<string, mixed>
     */
    public static function toArray(FilmEntity $entity): array
    {
        return [
            'uuid'                  => $entity->id->value,
            'titre'                 => $entity->titre,
            'titre_fr'              => $entity->titreOriginal,
            'realisateurs'          => $entity->realisateurs,
            'acteurs_principaux'    => $entity->acteursPrincipaux,
            'genres'                => $entity->genres,
            'duree_minutes'         => $entity->dureeMinutes,
            'classification'        => $entity->classification,
            'langue_originale'      => $entity->langueOriginale,
            'sous_titres'           => $entity->sousTitres,
            'resume'                => $entity->synopsis,
            'date_sortie'           => $entity->dateSortie->format('Y-m-d'),
            'date_fin_exploitation' => $entity->dateFinExploitation?->format('Y-m-d'),
            'note_presse'           => $entity->noteCritique,
            'note_public'           => $entity->notePublic,
            'note_moyenne_avis'     => $entity->noteMoyenneAvis,
            'nombre_avis'           => $entity->nombreAvis,
            'affiche_url'           => $entity->afficheUrl,
            'bande_annonce_url'     => $entity->bandeAnnonceUrl,
            'est_actif'             => $entity->estActif,
        ];
    }

    /**
     * Decode acteurs from double-encoded JSON
     */
    private static function decodeActeurs(?string $acteurs): array
    {
        if (empty($acteurs)) {
            return [];
        }

        $decoded = json_decode($acteurs, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Assign Entity data to Model (factorized logic)
     */
    private static function assignEntityDataToModel(FilmEntity $entity, FilmModel &$model): void
    {
        $model->uuid               = $entity->id->value;
        $model->titre              = $entity->titre;
        $model->titre_original     = $entity->titreOriginal;
        $model->realisateurs       = $entity->realisateurs;
        $model->acteurs_principaux = $entity->acteursPrincipaux;
        $model->genres             = $entity->genres;
        $model->duree_minutes      = $entity->dureeMinutes;
        $model->classification     = $entity->classification;
        $model->langue_originale   = $entity->langueOriginale;
        $model->pays_origine       = $entity->paysOrigine ?? 'France';
        $model->producteur         = $entity->producteur;
        $model->synopsis           = $entity->synopsis;
        $model->date_sortie        = $entity->dateSortie;
        $model->note_critique      = $entity->noteCritique;
        $model->note_public        = $entity->notePublic;
        $model->statut             = $entity->statut ?? 'A_VENIR';
        $model->affiche_url        = $entity->afficheUrl;
        $model->bande_annonce_url  = $entity->bandeAnnonceUrl;
        $model->est_actif          = $entity->estActif;
    }
}
