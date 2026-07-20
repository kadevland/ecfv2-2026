<?php

namespace App\Domain\Public\Repositories;

interface ReservationRepositoryInterface
{
    /**
     * Create a new reservation
     */
    public function create(array $data);

    /**
     * Find a reservation by ID
     */
    public function find($id);

    /**
     * Update a reservation
     */
    public function update($id, array $data);

    /**
     * Delete a reservation
     */
    public function delete($id);

    /**
     * Find reservations by user
     */
    public function findByUser($userId);

    /**
     * Find reservations by seance
     */
    public function findBySeance($seanceId);

    /**
     * Find reservation by code
     */
    public function findByCode($code);

    /**
     * Find reservation by user and seance
     */
    public function findByUserAndSeance($userId, $seanceId);

    /**
     * Get reserved places for a seance
     */
    public function getReservedPlaces($seanceId);

    /**
     * Get available places count for a seance
     */
    public function getAvailableCount($seanceId);

    /**
     * Lock places for a user
     */
    public function lockPlaces($seanceId, $places, $userId);

    /**
     * Release locked places
     */
    public function releaseLockedPlaces($seanceId, $places);

    /**
     * Confirm a reservation
     */
    public function confirm($reservationId);

    /**
     * Cancel a reservation
     */
    public function cancel($reservationId);

    /**
     * Update reservation status
     */
    public function updateStatus($reservationId, $status);

    /**
     * Get reservation statistics for a seance
     */
    public function getReservationStats($seanceId);

    /**
     * Get occupancy rate for a seance
     */
    public function getOccupancyRate($seanceId);
}
