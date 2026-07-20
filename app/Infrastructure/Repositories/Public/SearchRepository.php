<?php

namespace App\Infrastructure\Repositories\Public;

use App\Domain\Public\Repositories\SearchRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SearchRepository implements SearchRepositoryInterface
{
    /**
     * Search films with query and filters
     */
    public function searchFilms($query, $filters = [])
    {
        $db = DB::connection('mongodb')->table('film_catalogues');

        if ($query) {
            $db = $db->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'));
        }

        if (isset($filters['genre_id'])) {
            $db = $db->where('genre_id', $filters['genre_id']);
        }

        if (isset($filters['min_rating'])) {
            $db = $db->where('note_moyenne', '>=', $filters['min_rating']);
        }

        return $db->orderBy('created_at', 'desc')->get();
    }

    /**
     * Search seances
     */
    public function searchSeances($query)
    {
        return DB::connection('mongodb')
            ->table('seance_publics')
            ->where('film_titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->orderBy('date_seance', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();
    }

    /**
     * Global search
     */
    public function searchAll($query)
    {
        // Search in films
        $films = DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->get();

        // Search in seances
        $seances = DB::connection('mongodb')
            ->table('seance_publics')
            ->where('film_titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->get();

        return [
            'films' => $films,
            'seances' => $seances
        ];
    }

    /**
     * Search by actor name
     */
    public function searchByActor($actorName)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('acteurs.nom', 'regex', new \MongoDB\BSON\Regex($actorName, 'i'))
            ->orderBy('titre', 'asc')
            ->get();
    }

    /**
     * Search by director name
     */
    public function searchByDirector($directorName)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('realisateur.nom', 'regex', new \MongoDB\BSON\Regex($directorName, 'i'))
            ->orderBy('titre', 'asc')
            ->get();
    }

    /**
     * Search by cast member name
     */
    public function searchByCastMember($name)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('acteurs.nom', 'regex', new \MongoDB\BSON\Regex($name, 'i'))
            ->orderBy('titre', 'asc')
            ->get();
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch($criteria)
    {
        $db = DB::connection('mongodb')->table('film_catalogues');

        if (isset($criteria['query'])) {
            $db = $db->where('titre', 'regex', new \MongoDB\BSON\Regex($criteria['query'], 'i'));
        }

        if (isset($criteria['genre_id'])) {
            $db = $db->where('genre_id', $criteria['genre_id']);
        }

        if (isset($criteria['min_rating'])) {
            $db = $db->where('note_moyenne', '>=', $criteria['min_rating']);
        }

        if (isset($criteria['max_rating'])) {
            $db = $db->where('note_moyenne', '<=', $criteria['max_rating']);
        }

        if (isset($criteria['date_from'])) {
            $db = $db->where('date_sortie', '>=', $criteria['date_from']);
        }

        if (isset($criteria['date_to'])) {
            $db = $db->where('date_sortie', '<=', $criteria['date_to']);
        }

        if (isset($criteria['status'])) {
            $db = $db->where('status', $criteria['status']);
        }

        return $db->orderBy('created_at', 'desc')->get();
    }

    /**
     * Search by genre and rating
     */
    public function searchByGenreAndRating($genreId, $minRating)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('genre_id', $genreId)
            ->where('note_moyenne', '>=', $minRating)
            ->orderBy('note_moyenne', 'desc')
            ->get();
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10)
    {
        // This would typically query a search_logs table
        // For now, return empty array as it's not critical
        return [];
    }

    /**
     * Log search query
     */
    public function logSearch($query, $userId = null)
    {
        // Optional: Implement search logging for analytics
        try {
            DB::connection('mongodb')
                ->table('search_logs')
                ->insert([
                    'query' => $query,
                    'user_id' => $userId,
                    'created_at' => now()
                ]);
        } catch (\Exception $e) {
            // Silently fail as search logging is not critical
        }
    }

    /**
     * Quick search for modal
     */
    public function quickSearch($query, $limit = 10)
    {
        $regexPattern = new \MongoDB\BSON\Regex($query, 'i');

        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where(function ($q) use ($regexPattern) {
                $q->orWhere('titre', 'regex', $regexPattern)
                    ->orWhere('realisateurs', 'regex', $regexPattern)
                    ->orWhere('acteurs_principaux', 'regex', $regexPattern);
            })
            ->select(['film_id', 'titre', 'affiche_url', 'duree_minutes', 'genres', 'note_moyenne_avis', 'realisateurs', 'acteurs_principaux'])
            ->limit($limit)
            ->get();
    }

    /**
     * Autocomplete suggestions
     */
    public function autocomplete($term, $limit = 10)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('titre', 'regex', new \MongoDB\BSON\Regex($term, 'i'))
            ->orderBy('titre', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Instant search for HTMX (with en_diffusion filter)
     */
    public function instantSearch($query, $limit = 6)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->where('en_diffusion', true)
            ->select([
                'film_id', 'titre', 'affiche_url', 'note_moyenne',
                'duree_minutes', 'genres', 'classification', 'synopsis'
            ])
            ->limit($limit)
            ->get();
    }
}
