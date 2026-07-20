<?php

namespace App\Infrastructure\Repositories\Public;

use App\Domain\Public\Repositories\HealthCheckRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HealthCheckRepository implements HealthCheckRepositoryInterface
{
    /**
     * Check database connection
     */
    public function checkConnection()
    {
        try {
            DB::connection('pgsql')->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if required tables exist
     */
    public function checkTables()
    {
        $requiredTables = [
            'users',
            'cinemas',
            'salles',
            'films',
            'seances',
            'reservations'
        ];

        $tableStatus = [];
        foreach ($requiredTables as $table) {
            try {
                $exists = DB::connection('pgsql')
                    ->table("information_schema.tables")
                    ->where("table_schema", "public")
                    ->where("table_name", $table)
                    ->exists();
                $tableStatus[$table] = $exists;
            } catch (\Exception $e) {
                $tableStatus[$table] = false;
            }
        }

        return $tableStatus;
    }

    /**
     * Get database information
     */
    public function getDatabaseInfo()
    {
        try {
            $version = DB::connection('pgsql')->select("SELECT version()")[0]->version;
            $dbName = DB::connection('pgsql')->getDatabaseName();
            
            return [
                'database' => $dbName,
                'version' => $version,
                'connection' => 'pgsql',
                'status' => 'connected'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ping database
     */
    public function ping()
    {
        try {
            DB::connection('pgsql')->select("SELECT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        $health = [
            'database' => [
                'status' => 'unknown',
                'message' => 'Checking...'
            ],
            'mongodb' => [
                'status' => 'unknown',
                'message' => 'Checking...'
            ],
            'timestamp' => now()->toIso8601String()
        ];

        // Check PostgreSQL
        try {
            DB::connection('pgsql')->select("SELECT 1");
            $health['database'] = [
                'status' => 'healthy',
                'message' => 'PostgreSQL connection successful'
            ];
        } catch (\Exception $e) {
            $health['database'] = [
                'status' => 'unhealthy',
                'message' => 'PostgreSQL connection failed: ' . $e->getMessage()
            ];
        }

        // Check MongoDB
        try {
            DB::connection('mongodb')->table('film_catalogues')->count();
            $health['mongodb'] = [
                'status' => 'healthy',
                'message' => 'MongoDB connection successful'
            ];
        } catch (\Exception $e) {
            $health['mongodb'] = [
                'status' => 'unhealthy',
                'message' => 'MongoDB connection failed: ' . $e->getMessage()
            ];
        }

        return $health;
    }
}
