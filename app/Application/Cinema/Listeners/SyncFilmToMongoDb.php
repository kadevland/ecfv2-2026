<?php

declare(strict_types=1);

namespace App\Application\Cinema\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Domain\Cinema\Events\FilmCreated;
use App\Domain\Cinema\Events\FilmDeleted;
use App\Domain\Cinema\Events\FilmUpdated;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;

final class SyncFilmToMongoDb
{
    public function handleFilmCreated(FilmCreated $event): void
    {
        try {
            $filmUuid = $event->getFilmUuid();

            // Charger depuis PostgreSQL
            $filmModel = FilmModel::where('uuid', $filmUuid)->first();
            if (!$filmModel) {
                Log::warning('Film not found in PostgreSQL for sync', ['film_uuid' => $filmUuid]);

                return;
            }

            // Synchroniser vers MongoDB
            FilmCatalogue::create([
                '_id'                    => $filmUuid,
                'film_id'                => $filmUuid,
                'titre'                  => $filmModel->titre,
                'titre_original'         => $filmModel->titre_original,
                'description'            => $filmModel->synopsis,
                'synopsis'               => $filmModel->synopsis,
                'genre'                  => implode(', ', $filmModel->genres),
                'genres'                 => $filmModel->genres,
                'duree'                  => $filmModel->duree_minutes,
                'duree_minutes'          => $filmModel->duree_minutes,
                'classification'         => $filmModel->classification,
                'langue_originale'       => $filmModel->langue_originale,
                'pays_origine'           => $filmModel->pays_origine,
                'date_sortie'            => $filmModel->date_sortie,
                'date_fin_exploitation'  => $filmModel->date_fin_exploitation,
                'realisateur'            => implode(', ', $filmModel->realisateurs),
                'realisateurs'           => $filmModel->realisateurs,
                'acteurs_principaux'     => $filmModel->acteurs_principaux,
                'sous_titres'            => $filmModel->sous_titres ?? [],
                'producteur'             => $filmModel->producteur,
                'images_additionnelles'  => $filmModel->images_additionnelles ?? [],
                'metadonnees_techniques' => $filmModel->metadonnees_techniques ?? [],
                'affiche_url'            => $filmModel->affiche_url,
                'bande_annonce_url'      => $filmModel->bande_annonce_url,
                'note_critique'          => $filmModel->note_critique,
                'note_public'            => $filmModel->note_public,
                'note_moyenne'           => $filmModel->note_moyenne_avis ?? 0,
                'note_moyenne_avis'      => $filmModel->note_moyenne_avis ?? 0,
                'nombre_avis'            => $filmModel->nombre_avis ?? 0,
                'statut_diffusion'       => $filmModel->est_actif ? 'en_diffusion' : 'arrete',
                'statut'                 => $filmModel->statut,
                'est_actif'              => $filmModel->est_actif,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            Log::info('Film created in MongoDB', ['film_uuid' => $filmUuid]);
        } catch (Exception $e) {
            Log::error('Failed to create film in MongoDB', [
                'film_uuid' => $event->getFilmUuid(),
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function handleFilmUpdated(FilmUpdated $event): void
    {
        try {
            Log::info('=== DEBUG: handleFilmUpdated CALLED ===');
            $filmUuid = $event->getFilmUuid();
            Log::info('Film UUID from event', ['film_uuid' => $filmUuid]);

            // Charger depuis PostgreSQL
            $filmModel = FilmModel::where('uuid', $filmUuid)->first();
            if (!$filmModel) {
                Log::warning('Film not found in PostgreSQL for sync', ['film_uuid' => $filmUuid]);

                return;
            }

            Log::info('Film loaded from PostgreSQL', ['titre' => $filmModel->titre]);

            // Vérifier si le film existe déjà dans MongoDB
            $existingFilm = FilmCatalogue::where('_id', $filmUuid)->first();
            if ($existingFilm) {
                Log::info('Film exists in MongoDB, updating...', ['existing_titre' => $existingFilm->titre]);

                // Mettre à jour
                $updated = FilmCatalogue::where('_id', $filmUuid)->update([
                    'titre'                  => $filmModel->titre,
                    'titre_original'         => $filmModel->titre_original,
                    'description'            => $filmModel->synopsis,
                    'synopsis'               => $filmModel->synopsis,
                    'genre'                  => implode(', ', $filmModel->genres),
                    'genres'                 => $filmModel->genres,
                    'duree'                  => $filmModel->duree_minutes,
                    'duree_minutes'          => $filmModel->duree_minutes,
                    'classification'         => $filmModel->classification,
                    'langue_originale'       => $filmModel->langue_originale,
                    'pays_origine'           => $filmModel->pays_origine,
                    'date_sortie'            => $filmModel->date_sortie,
                    'date_fin_exploitation'  => $filmModel->date_fin_exploitation,
                    'realisateur'            => implode(', ', $filmModel->realisateurs),
                    'realisateurs'           => $filmModel->realisateurs,
                    'acteurs_principaux'     => $filmModel->acteurs_principaux,
                    'sous_titres'            => $filmModel->sous_titres ?? [],
                    'producteur'             => $filmModel->producteur,
                    'images_additionnelles'  => $filmModel->images_additionnelles ?? [],
                    'metadonnees_techniques' => $filmModel->metadonnees_techniques ?? [],
                    'affiche_url'            => $filmModel->affiche_url,
                    'bande_annonce_url'      => $filmModel->bande_annonce_url,
                    'note_critique'          => $filmModel->note_critique,
                    'note_public'            => $filmModel->note_public,
                    'note_moyenne'           => $filmModel->note_moyenne_avis ?? 0,
                    'note_moyenne_avis'      => $filmModel->note_moyenne_avis ?? 0,
                    'nombre_avis'            => $filmModel->nombre_avis ?? 0,
                    'statut_diffusion'       => $filmModel->est_actif ? 'en_diffusion' : 'arrete',
                    'statut'                 => $filmModel->statut,
                    'est_actif'              => $filmModel->est_actif,
                    'updated_at'             => now(),
                ]);

                Log::info('Update result', ['updated_count' => $updated]);
            } else {
                Log::info('Film not found in MongoDB, creating new...');

                // Créer nouveau
                $created = FilmCatalogue::create([
                    '_id'                    => $filmUuid,
                    'film_id'                => $filmUuid,
                    'titre'                  => $filmModel->titre,
                    'titre_original'         => $filmModel->titre_original,
                    'description'            => $filmModel->synopsis,
                    'synopsis'               => $filmModel->synopsis,
                    'genre'                  => implode(', ', $filmModel->genres),
                    'genres'                 => $filmModel->genres,
                    'duree'                  => $filmModel->duree_minutes,
                    'duree_minutes'          => $filmModel->duree_minutes,
                    'classification'         => $filmModel->classification,
                    'langue_originale'       => $filmModel->langue_originale,
                    'pays_origine'           => $filmModel->pays_origine,
                    'date_sortie'            => $filmModel->date_sortie,
                    'date_fin_exploitation'  => $filmModel->date_fin_exploitation,
                    'realisateur'            => implode(', ', $filmModel->realisateurs),
                    'realisateurs'           => $filmModel->realisateurs,
                    'acteurs_principaux'     => $filmModel->acteurs_principaux,
                    'sous_titres'            => $filmModel->sous_titres ?? [],
                    'producteur'             => $filmModel->producteur,
                    'images_additionnelles'  => $filmModel->images_additionnelles ?? [],
                    'metadonnees_techniques' => $filmModel->metadonnees_techniques ?? [],
                    'affiche_url'            => $filmModel->affiche_url,
                    'bande_annonce_url'      => $filmModel->bande_annonce_url,
                    'note_critique'          => $filmModel->note_critique,
                    'note_public'            => $filmModel->note_public,
                    'note_moyenne'           => $filmModel->note_moyenne_avis ?? 0,
                    'note_moyenne_avis'      => $filmModel->note_moyenne_avis ?? 0,
                    'nombre_avis'            => $filmModel->nombre_avis ?? 0,
                    'statut_diffusion'       => $filmModel->est_actif ? 'en_diffusion' : 'arrete',
                    'statut'                 => $filmModel->statut,
                    'est_actif'              => $filmModel->est_actif,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);

                Log::info('Create result', ['created_id' => $created->_id ?? 'FAILED']);
            }

            Log::info('Film updated in MongoDB', ['film_uuid' => $filmUuid]);
        } catch (Exception $e) {
            Log::error('Failed to update film in MongoDB', [
                'film_uuid' => $event->getFilmUuid(),
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function handleFilmDeleted(FilmDeleted $event): void
    {
        try {
            $filmUuid = $event->getFilmUuid();

            FilmCatalogue::where('_id', $filmUuid)->update([
                'statut_diffusion' => 'supprime',
                'deleted_at'       => now(),
                'updated_at'       => now(),
            ]);

            Log::info('Film soft deleted in MongoDB', ['film_uuid' => $filmUuid]);
        } catch (Exception $e) {
            Log::error('Failed to soft delete film in MongoDB', [
                'film_uuid' => $event->getFilmUuid(),
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
