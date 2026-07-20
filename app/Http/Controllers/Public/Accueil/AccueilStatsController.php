<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Accueil;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

/**
 * Contrôleur pour les statistiques de l'accueil
 * RESPONSABILITÉ UNIQUE : Fournir les statistiques de la page d'accueil
 */
class AccueilStatsController extends Controller
{
    /**
     * Retourne les statistiques de l'accueil
     * GET /api/home/stats
     */
    public function __invoke(): JsonResponse
    {
        $stats = Cache::remember('homepage_stats', 10 * 60, function () {
            return [
                'films_total'      => FilmCatalogue::enDiffusion()->count(),
                'films_nouveautes' => FilmCatalogue::enDiffusion()
                    ->where('date_sortie', '>=', now()->subDays(7)->startOfDay())
                    ->count(),
                'genres_disponibles' => FilmCatalogue::enDiffusion()
                    ->distinct('genre')
                    ->count(),
                'note_moyenne' => round(
                    FilmCatalogue::enDiffusion()->avg('note_moyenne') ?? 0,
                    1
                ),
                'films_4k' => FilmCatalogue::enDiffusion()
                    ->where('prochaines_seances.qualite_projection', 'IMAX')
                    ->count(),
            ];
        });

        return response()->json($stats);
    }
}
