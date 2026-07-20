<?php

namespace App\Infrastructure\Repositories\Public;

use App\Domain\Public\Repositories\FilmRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FilmRepository implements FilmRepositoryInterface
{
    /**
     * Find a film by ID
     */
    public function find($id)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('_id', $id)
            ->first();
    }

    /**
     * Get all films
     */
    public function findAll()
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated films
     */
    public function findPaginated($page = 1, $limit = 20)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
    }

    /**
     * Search films by title
     */
    public function searchByTitle($query)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->orderBy('titre', 'asc')
            ->get();
    }

    /**
     * Find films by genre
     */
    public function findByGenre($genreId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('genre_id', $genreId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find films by actor
     */
    public function findByActor($actorId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('acteurs.id', $actorId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find films by director
     */
    public function findByDirector($directorId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('realisateur.id', $directorId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find films by date range
     */
    public function findByDateRange($startDate, $endDate)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->whereBetween('date_sortie', [$startDate, $endDate])
            ->orderBy('date_sortie', 'desc')
            ->get();
    }

    /**
     * Find films by minimum rating
     */
    public function findByRating($minRating)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('note_moyenne', '>=', $minRating)
            ->orderBy('note_moyenne', 'desc')
            ->get();
    }

    /**
     * Get now playing films
     */
    public function findNowPlaying()
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('status', 'now_playing')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get upcoming films
     */
    public function findUpcoming()
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('status', 'upcoming')
            ->orderBy('date_sortie', 'asc')
            ->get();
    }

    /**
     * Find films by status
     */
    public function findByStatus($status)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search films with filters
     */
    public function searchWithFilters($query, $filters = [])
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

        if (isset($filters['status'])) {
            $db = $db->where('status', $filters['status']);
        }

        return $db->orderBy('created_at', 'desc')->get();
    }

    /**
     * Autocomplete for quick search
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
     * Get film details with relations
     */
    public function getFilmDetails($filmId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('_id', $filmId)
            ->first();
    }

    /**
     * Get films carousel data
     */
    public function getFilmsCarousel($limit = 10)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('status', 'now_playing')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get film suggestions
     */
    public function getSuggestions($limit = 5)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('note_moyenne', '>=', 4.0)
            ->orderBy('note_moyenne', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Store a film rating
     */
    public function storeRating($filmId, array $ratingData)
    {
        return DB::connection('mongodb')
            ->table('film_ratings')
            ->insert($ratingData);
    }

    /**
     * Get film ratings
     */
    public function getRatings($filmId)
    {
        return DB::connection('mongodb')
            ->table('film_ratings')
            ->where('film_id', $filmId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated films with filters for catalogue index
     */
    public function getFilmsCatalogueIndex($search = null, $genre = null, $classification = null, $perPage = 12)
    {
        $query = DB::connection('mongodb')->table('film_catalogues');

        // Apply search filter
        if ($search) {
            $query->where('titre', 'regex', new \MongoDB\BSON\Regex($search, 'i'));
        }

        // Apply genre filter
        if ($genre) {
            $query->where('genre', 'regex', new \MongoDB\BSON\Regex($genre, 'i'));
        }

        // Apply classification filter
        if ($classification) {
            $query->where('classification', $classification);
        }

        // Apply sorting - only by date
        $query->orderBy('date_sortie', 'desc');

        // Paginate
        return $query->paginate($perPage);
    }

    /**
     * Get films by genre with pagination
     */
    public function getFilmsByGenrePaginated($genre, $perPage = 12)
    {
        return DB::connection('mongodb')->table('film_catalogues')
            ->where('genre', 'regex', new \MongoDB\BSON\Regex($genre, 'i'))
            ->orderBy('date_sortie', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find film by film_id field
     */
    public function findByFilmId($filmId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('film_id', $filmId)
            ->first();
    }

    /**
     * Check if film exists by film_id
     */
    public function filmExists($filmId)
    {
        return DB::connection('mongodb')
            ->table('film_catalogues')
            ->where('film_id', $filmId)
            ->exists();
    }

    /**
     * Get approved reviews for a film
     */
    public function getApprovedReviews($filmId)
    {
        return DB::connection('mongodb')
            ->table('avis_films')
            ->where('film_id', $filmId)
            ->where('statut', 'approuve')
            ->orderBy('date_creation', 'desc')
            ->get();
    }

    /**
     * Insert film review
     */
    public function insertReview(array $reviewData)
    {
        return DB::connection('mongodb')
            ->table('avis_films')
            ->insert($reviewData);
    }
}
