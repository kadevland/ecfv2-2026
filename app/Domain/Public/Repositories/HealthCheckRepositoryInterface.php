<?php

namespace App\Domain\Public\Repositories;

interface HealthCheckRepositoryInterface
{
    /**
     * Check database connection
     */
    public function checkConnection();

    /**
     * Check if required tables exist
     */
    public function checkTables();

    /**
     * Get database information
     */
    public function getDatabaseInfo();

    /**
     * Ping database
     */
    public function ping();

    /**
     * Get system health status
     */
    public function getSystemHealth();
}
