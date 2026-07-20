<?php

namespace App\Domain\Public\Repositories;

interface SeanceRepositoryInterface
{

    /**
     * Get seance public data
     */
    public function getSeancePublicData ($seanceId);

    /**
     * Get film seances for public view
     */
    public function getFilmSeancesPublic ($filmId, $date = null);

    /**
     * Get seances for index page with filters
     */
    public function getSeancesForIndex ($filters = []);

    /**
     * Get available cinemas for filters
     */
    public function getAvailableCinemas ();

    /**
     * Get films by IDs for seances
     */
    public function getFilmsByIds (array $filmIds);

    /**
     * Get films by genre
     */
    public function getFilmsByGenre ($genre);

    /**
     * Get unique genre tags from all films (PHP processing)
     */
    public function getUniqueGenres () : array;


    public function getSeanceUpcomingForFilm ($filmId);
    public function getSeanceForFilm ($filmId, $cinema = null, $date = null);
    public function getSeanceCinemaForFilm ($filmId);
}
