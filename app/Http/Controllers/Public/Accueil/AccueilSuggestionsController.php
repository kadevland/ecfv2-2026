<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Accueil;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MongoDB\FilmCatalogueQueryService;

/**
 * Contrôleur pour les suggestions de films de l'accueil
 * RESPONSABILITÉ UNIQUE : Fournir des suggestions personnalisées
 */
class AccueilSuggestionsController extends Controller
{
    public function __construct(
        private FilmCatalogueQueryService $filmService
    ) {}

    /**
     * Retourne des suggestions de films personnalisées
     * GET /api/home/suggestions
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Pour l'instant, suggestions génériques
        // Plus tard : basées sur l'utilisateur connecté
        $userPreferences = [
            'genres'       => $request->get('genres', ['action', 'adventure']),
            'min_rating'   => $request->get('min_rating', 3.0),
            'max_duration' => $request->get('max_duration', 180),
        ];

        $suggestions = $this->filmService->getPersonalizedRecommendations(
            $userPreferences,
            6
        );

        return response()->json([
            'preferences' => $userPreferences,
            'suggestions' => $suggestions,
        ]);
    }
}
