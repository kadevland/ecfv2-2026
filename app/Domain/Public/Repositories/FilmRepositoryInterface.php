<?php

namespace App\Domain\Public\Repositories;

interface FilmRepositoryInterface
{
    /**
     * Find a film by ID
     */
    public function find($id);

    /**
     * Get all films
     */
    public function findAll();

    /**
     * Get paginated films
     */
    public function findPaginated($page = 1, $limit = 20);

    /**
     * Search films by title
     */
    public function searchByTitle($query);

    /**
     * Find films by genre
     */
    public function findByGenre($genreId);

    /**
     * Find films by actor
     */
    public function findByActor($actorId);

    /**
     * Find films by director
     */
    public function findByDirector($directorId);

    /**
     * Find films by date range
     */
    public function findByDateRange($startDate, $endDate);

    /**
     * Find films by minimum rating
     */
    public function findByRating($minRating);

    /**
     * Get now playing films
     */
    public function findNowPlaying();

    /**
     * Get upcoming films
     */
    public function findUpcoming();

    /**
     * Find films by status
     */
    public function findByStatus($status);

    /**
     * Search films with filters
     */
    public function searchWithFilters($query, $filters = []);

    /**
     * Autocomplete for quick search
     */
    public function autocomplete($term, $limit = 10);

    /**
     * Get film details with relations
     */
    public function getFilmDetails($filmId);

    /**
     * Get films carousel data
     */
    public function getFilmsCarousel($limit = 10);

    /**
     * Get film suggestions
     */
    public function getSuggestions($limit = 5);

    /**
     * Store a film rating
     */
    public function storeRating($filmId, array $ratingData);

    /**
     * Get film ratings
     */
    public function getRatings($filmId);

    /**
     * Get paginated films with filters for catalogue index
     */
    public function getFilmsCatalogueIndex($search = null, $genre = null, $classification = null, $perPage = 12);

    /**
     * Get films by genre with pagination
     */
    public function getFilmsByGenrePaginated($genre, $perPage = 12);

    /**
     * Find film by film_id field
     */
    public function findByFilmId($filmId);

    /**
     * Check if film exists by film_id
     */
    public function filmExists($filmId);

    /**
     * Get approved reviews for a film
     */
    public function getApprovedReviews($filmId);

    /**
     * Insert film review
     */
    public function insertReview(array $reviewData);
}
