<?php

namespace App\Domain\Public\Repositories;

interface SalleRepositoryInterface
{
    /**
     * Find a salle by ID
     */
    public function find($id);

    /**
     * Get all salles
     */
    public function findAll();

    /**
     * Get salle capacity
     */
    public function getCapacity($salleId);

    /**
     * Find salles by cinema
     */
    public function findByCinema($cinemaId);

    /**
     * Get available salles for date and time
     */
    public function getAvailableSalles($date, $time);

    /**
     * Get salle with full details
     */
    public function getSalleWithDetails($salleId);

    /**
     * Find salles by city
     */
    public function findByCity($cityId);
}
