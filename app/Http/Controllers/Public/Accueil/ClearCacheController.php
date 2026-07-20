<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Accueil;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Contrôleur pour vider le cache de l'accueil
 * RESPONSABILITÉ UNIQUE : Vider le cache de la page d'accueil
 */
class ClearCacheController extends Controller
{
    /**
     * Vide le cache de la page d'accueil
     * POST /api/home/clear-cache
     */
    public function __invoke(): JsonResponse
    {
        Cache::forget('homepage_data');
        Cache::forget('homepage_stats');

        // Vider aussi les caches de recherche
        $pattern = 'search_*';
        if (config('cache.default') === 'redis') {
            /** @phpstan-ignore-next-line staticMethod.notFound */
            $keys = Cache::getRedis()->keys($pattern);
            if ($keys) {
                /** @phpstan-ignore-next-line staticMethod.notFound */
                Cache::getRedis()->del($keys);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully',
        ]);
    }
}
