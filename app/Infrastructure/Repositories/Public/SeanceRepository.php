<?php

namespace App\Infrastructure\Repositories\Public;

use App\Domain\Public\Repositories\SeanceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SeanceRepository implements SeanceRepositoryInterface
{


    /**
     * Get seance public data
     */
    public function getSeancePublicData ($seanceId)
    {
        return DB::connection('mongodb')
            ->table('seance_publics')
            ->where('seance_id', $seanceId)
            ->first();
    }

    /**
     * Get film seances for public view
     */
    public function getFilmSeancesPublic ($filmId, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        return DB::connection('mongodb')
            ->table('seance_publics')
            ->where('film_id', $filmId)
            ->where('date_seance', $date)
            ->orderBy('heure_debut', 'asc')
            ->get();
    }

    /**
     * Get seances for index page with filters
     */
    public function getSeancesForIndex ($filters = [])
    {
        $query = DB::connection('mongodb')
            ->table('seance_publics')
            ->where('date_heure_debut', '>', now()->toDateTime())
            ->orderBy('date_heure_debut');

        // Appliquer le filtre de date si fourni
        if (isset($filters['date']) && $filters['date']) {
            $query->whereDate('date_heure_debut', $filters['date']);
        }

        // Appliquer le filtre de cinéma si fourni
        if (isset($filters['cinema']) && $filters['cinema']) {
            $query->where('cinema_id', (string) $filters['cinema']);
        }

        // Appliquer le filtre de genre si fourni
        if (isset($filters['genre']) && $filters['genre']) {
            $filmsWithGenre = DB::connection('mongodb')
                ->table('film_catalogues')
                ->where('genres', 'like', '%' . $filters['genre'] . '%')
                ->pluck('film_id');

            $query->whereIn('film_id', $filmsWithGenre);
        }

        return $query->get();
    }

    /**
     * Get available cinemas for filters
     */
    public function getAvailableCinemas () : array
    {
        return DB::connection('mongodb')
            ->table('seance_publics')
            ->where('date_heure_debut', '>', now()->toDateTime())
            ->get(['cinema_id', 'cinema_nom'])
            ->unique('cinema_id')
            ->pluck('cinema_nom', 'cinema_id')
            ->filter()
            ->sort()
            ->toArray();
    }

    /**
     * Get films by IDs for seances
     */
    public function getFilmsByIds (array $filmIds)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->whereIn('film_id', $filmIds)
            ->get()
            ->keyBy('film_id');
    }

    /**
     * Get films by genre
     */
    public function getFilmsByGenre ($genre)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('genres', 'like', '%' . $genre . '%')
            ->pluck('genres')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * Get unique genre tags from all films (PHP processing)
     */
    public function getUniqueGenres () : array
    {
        // Récupérer tous les genres depuis MongoDB
        $allGenres = DB::connection('mongodb')
            ->table('film_catalogues')
            ->pluck('genres')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();

        // Traiter en PHP pour créer un tableau simple de tags uniques
        $genreTags = [];
        foreach ($allGenres as $genreArray) {

            $values = json_decode($genreArray, true); // Décoder le JSON si nécessaire

            if (is_array($values)) {
                foreach ($values as $tag) {
                    if (is_string($tag) && !in_array($tag, $genreTags)) {
                        $genreTags[] = $tag;
                    }
                }
            } elseif (is_string($genreArray) && !in_array($genreArray, $genreTags)) {
                $genreTags[] = $genreArray;
            }
        }

        return $genreTags;
    }

    /**
     * Get upcoming seances for a specific film
     */
    public function getSeanceUpcomingForFilm ($filmId)
    {

        return DB::connection('mongodb')->table('seance_publics')
            ->where('film_id', $filmId)
            ->where('date_heure_debut', '>=', now()->startOfDay())
            ->whereIn('statut', ['PROGRAMMEE'])
            ->orderBy('date_heure_debut')
            ->get();
    }

    public function getSeanceForFilm ($filmId, $cinema = null, $date = null)
    {

        // Récupérer les séances du film depuis la collection séparée avec filtres
        $seancesQuery = DB::connection('mongodb')->table('seance_publics')
            ->where('film_id', $filmId);

        // Si cinema est vide, on l'ignore (afficher tous les cinémas)
        if (!empty($cinema)) {
            $seancesQuery->where('cinema_id', (string) $cinema);
        }

        // Si date est spécifiée et non vide, filtrer sur cette date
        if (!empty($date)) {
            // Filtrer sur la date spécifiée
            $seancesQuery->whereDate('date_heure_debut', $date);
        } else {
            // Si pas de date ou date vide, afficher à partir d'aujourd'hui
            $seancesQuery->where('date_heure_debut', '>=', now()->startOfDay());
        }

        $seancesQuery->orderBy('date_heure_debut');

        return $seancesQuery->get();
    }


    public function getSeanceCinemaForFilm ($filmId)
    {

        $cinemasData = DB::connection('mongodb')->table('seance_publics')
            ->where('film_id', $filmId)
            ->where('date_heure_debut', '>=', now()->startOfDay())
            ->whereIn('statut', ['PROGRAMMEE'])
            ->get(['cinema_id', 'cinema_nom']);

        // dd($cinemasData);

        return $cinemasData->unique('cinema_id')
            ->map(function ($cinema) {
                // Récupérer les détails complets du cinéma depuis MongoDB
                $cinemaDetails = DB::connection('mongodb')->table('cinema_publics')
                    ->where('cinema_id', $cinema->cinema_id)
                    ->first();

                // Vérifier si le cinéma est actif
                if (!$cinemaDetails || ($cinemaDetails->statut ?? '') !== 'actif') {
                    return null;
                }

                return [
                    'id'        => $cinema->cinema_id,
                    'nom'       => $cinema->cinema_nom,
                    'adresse'   => $cinemaDetails->adresse ?? 'Adresse non disponible',
                    'ville'     => $cinemaDetails->ville ?? '',
                    'telephone' => $cinemaDetails->telephone ?? '',
                ];
            })
            ->filter() // Retirer les cinémas null (non actifs)
            ->values();

    }
}
