<?php

namespace App\Domain\Public\Repositories;

interface SearchRepositoryInterface
{
    /**
     * Search films with query and filters
     */
    public function searchFilms($query, $filters = []);

    /**
     * Search seances
     */
    public function searchSeances($query);

    /**
     * Global search
     */
    public function searchAll($query);

    /**
     * Search by actor name
     */
    public function searchByActor($actorName);

    /**
     * Search by director name
     */
    public function searchByDirector($directorName);

    /**
     * Search by cast member name
     */
    public function searchByCastMember($name);

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch($criteria);

    /**
     * Search by genre and rating
     */
    public function searchByGenreAndRating($genreId, $minRating);

    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10);

    /**
     * Log search query
     */
    public function logSearch($query, $userId = null);

    /**
     * Quick search for modal
     */
    public function quickSearch($query, $limit = 10);

    /**
     * Autocomplete suggestions
     */
    public function autocomplete($term, $limit = 10);

    /**
     * Instant search for HTMX (with en_diffusion filter)
     */
    public function instantSearch($query, $limit = 6);
}
