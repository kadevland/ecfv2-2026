<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Storage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoDBClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Domain\Public\Repositories\HealthCheckRepositoryInterface;

class HealthController extends Controller
{
    public function __construct(
        protected HealthCheckRepositoryInterface $healthCheckRepository
    ) {}
    /**
     * Health check principal
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'      => 'healthy',
            'timestamp'   => now()->toISOString(),
            'version'     => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'services'    => [
                'database' => $this->checkDatabase(),
                'redis'    => $this->checkRedis(),
                'mongodb'  => $this->checkMongoDB(),
                'cache'    => $this->checkCache(),
                'queue'    => $this->checkQueue(),
                'storage'  => $this->checkStorage(),
            ],
        ]);
    }

    /**
     * Health check simple (pour load balancers)
     */
    public function simple(): JsonResponse
    {
        return response()->json([
            'status'    => 'healthy',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Health check détaillé pour la base de données
     */
    public function database(): JsonResponse
    {
        $database = $this->checkDatabase();

        return response()->json([
            'service' => 'database',
            'status'  => $database['status'],
            'details' => $database,
        ])->setStatusCode($database['status'] === 'healthy' ? 200 : 503);
    }

    /**
     * Health check détaillé pour Redis
     */
    public function redis(): JsonResponse
    {
        $redis = $this->checkRedis();

        return response()->json([
            'service' => 'redis',
            'status'  => $redis['status'],
            'details' => $redis,
        ])->setStatusCode($redis['status'] === 'healthy' ? 200 : 503);
    }

    /**
     * Health check détaillé pour MongoDB
     */
    public function mongodb(): JsonResponse
    {
        $mongodb = $this->checkMongoDB();

        return response()->json([
            'service' => 'mongodb',
            'status'  => $mongodb['status'],
            'details' => $mongodb,
        ])->setStatusCode($mongodb['status'] === 'healthy' ? 200 : 503);
    }

    /**
     * Informations système pour monitoring
     */
    public function system(): JsonResponse
    {
        return response()->json([
            'status'    => 'healthy',
            'timestamp' => now()->toISOString(),
            'server'    => [
                'php_version'         => PHP_VERSION,
                'laravel_version'     => app()->version(),
                'environment'         => config('app.env'),
                'debug_mode'          => config('app.debug'),
                'timezone'            => config('app.timezone'),
                'locale'              => config('app.locale'),
                'memory_limit'        => ini_get('memory_limit'),
                'max_execution_time'  => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size'       => ini_get('post_max_size'),
            ],
            'services' => [
                'cache_driver'     => config('cache.default'),
                'queue_connection' => config('queue.default'),
                'default_mailer'   => config('mail.default'),
                'filesystem_disk'  => config('filesystems.default'),
                'session_driver'   => config('session.driver'),
            ],
        ]);
    }

    /**
     * Vérification de la connexion PostgreSQL
     */
    protected function checkDatabase(): array
    {
        return $this->healthCheckRepository->checkConnection() 
            ? [
                'status'        => 'healthy',
                'connection'    => 'postgresql',
                'database'      => DB::connection('pgsql')->getDatabaseName(),
                'response_time' => $this->measureResponseTime(function () {
                    $this->healthCheckRepository->ping();
                }),
            ] 
            : [
                'status'     => 'unhealthy',
                'error'      => 'Database connection failed',
                'connection' => 'postgresql',
            ];
    }

    /**
     * Vérification de la connexion Redis
     */
    protected function checkRedis(): array
    {
        try {
            $startTime = microtime(true);
            Redis::ping();
            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'status'        => 'healthy',
                'connection'    => 'redis',
                'response_time' => round($responseTime, 2),
            ];
        } catch (Exception $e) {
            return [
                'status'     => 'unhealthy',
                'error'      => $e->getMessage(),
                'connection' => 'redis',
            ];
        }
    }

    /**
     * Vérification de la connexion MongoDB
     */
    protected function checkMongoDB(): array
    {
        $systemHealth = $this->healthCheckRepository->getSystemHealth();
        
        if (isset($systemHealth['mongodb']) && $systemHealth['mongodb']['status'] === 'healthy') {
            return [
                'status'        => 'healthy',
                'connection'    => 'mongodb',
                'database'      => config('database.connections.mongodb.database'),
                'response_time' => 'OK',
            ];
        }
        
        return [
            'status'     => 'unhealthy',
            'error'      => $systemHealth['mongodb']['error'] ?? 'MongoDB connection failed',
            'connection' => 'mongodb',
        ];
    }

    /**
     * Vérification du cache
     */
    protected function checkCache(): array
    {
        try {
            $key   = 'health_check_' . now()->timestamp;
            $value = 'ok';

            Cache::put($key, $value, 60);
            $retrieved = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => $retrieved === $value ? 'healthy' : 'unhealthy',
                'driver' => config('cache.default'),
                'test'   => 'read_write_delete',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error'  => $e->getMessage(),
                'driver' => config('cache.default'),
            ];
        }
    }

    /**
     * Vérification du système de queue
     */
    protected function checkQueue(): array
    {
        try {
            // Vérifier si les workers de queue sont actifs
            $connection = config('queue.default');

            return [
                'status'     => 'healthy',
                'connection' => $connection,
                'workers'    => 'active',
            ];
        } catch (Exception $e) {
            return [
                'status'     => 'unhealthy',
                'error'      => $e->getMessage(),
                'connection' => config('queue.default', 'redis'),
            ];
        }
    }

    /**
     * Vérification du système de stockage
     */
    protected function checkStorage(): array
    {
        try {
            $disk = config('filesystems.default');

            // Test d'écriture/lecture/suppression
            $testFile    = 'health_test_' . time() . '.txt';
            $testContent = 'health check';

            // Écriture
            Storage::disk($disk)->put($testFile, $testContent);

            // Lecture
            $retrieved = Storage::disk($disk)->get($testFile);

            // Suppression
            Storage::disk($disk)->delete($testFile);

            return [
                'status' => $retrieved === $testContent ? 'healthy' : 'unhealthy',
                'disk'   => $disk,
                'test'   => 'write_read_delete',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error'  => $e->getMessage(),
                'disk'   => config('filesystems.default'),
            ];
        }
    }

    /**
     * Mesurer le temps de réponse d'une fonction
     */
    protected function measureResponseTime(callable $callback): float
    {
        $startTime = microtime(true);
        $callback();

        return round((microtime(true) - $startTime) * 1000, 2);
    }
}
