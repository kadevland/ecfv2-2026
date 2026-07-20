<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Accueil;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MongoDB\FilmCatalogueQueryService;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Models\MongoDB\SeancePublic;

/**
 * Contrôleur pour le carrousel de films de l'accueil
 * RESPONSABILITÉ UNIQUE : Fournir les données du carrousel de films
 */
class FilmsCarouselController extends Controller
{
    public function __construct(
        private FilmCatalogueQueryService $filmService
    ) {}

    /**
     * Retourne les films pour le carrousel
     * GET /api/home/films-carousel
     */
    public function __invoke(Request $request): JsonResponse
    {
        $type  = $request->get('type', 'featured');
        $limit = min((int) $request->get('limit', 8), 20);

        $films = match ($type) {
            // Films de la semaine courante (mercredi à mardi)
            'current-week' => $this->getFilmsCurrentWeek($limit),

            // Films à venir (2 prochaines semaines)
            'upcoming' => $this->getFilmsUpcoming($limit),

            // Fallback pour anciennes catégories
            'trending' => $this->filmService->getTrendingFilms(7, $limit),

            // Par défaut : films en diffusion avec séances disponibles
            default => $this->getFilmsWithAvailableSeances($limit)
        };

        return response()->json([
            'type'  => $type,
            'films' => $films,
            'count' => count($films),
        ]);
    }

    /**
     * Récupère les films de la semaine courante (mercredi à mardi)
     */
    private function getFilmsCurrentWeek(int $limit)
    {
        // Calculer le début de la semaine cinéma (dernier mercredi)
        $now              = now();
        $currentWeekStart = $now->copy()->startOfWeek(Carbon::WEDNESDAY);

        // Si on est avant mercredi, prendre le mercredi de la semaine précédente
        if ($now->dayOfWeek < Carbon::WEDNESDAY) {
            $currentWeekStart->subWeek();
        }

        $currentWeekEnd = $currentWeekStart->copy()->addDays(6); // Mardi suivant

        return FilmCatalogue::enDiffusion()
            ->where('prochaines_seances', 'exists', true)
            ->where('prochaines_seances', 'elemMatch', [
                'date_heure_debut' => [
                    '$gte' => $currentWeekStart->toISOString(),
                    '$lte' => $currentWeekEnd->endOfDay()->toISOString(),
                ],
            ])
            ->orderBy('note_moyenne', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les films à venir (2 prochaines semaines)
     */
    private function getFilmsUpcoming(int $limit)
    {
        // Début : prochain mercredi après la semaine courante
        $now              = now();
        $currentWeekStart = $now->copy()->startOfWeek(Carbon::WEDNESDAY);

        if ($now->dayOfWeek < Carbon::WEDNESDAY) {
            $currentWeekStart->subWeek();
        }

        $upcomingStart = $currentWeekStart->copy()->addWeek(); // Mercredi suivant
        $upcomingEnd   = $upcomingStart->copy()->addWeeks(2); // 2 semaines plus tard

        return FilmCatalogue::where('statut', 'BIENTOT_DISPONIBLE')
            ->orWhere(function ($query) use ($upcomingStart, $upcomingEnd) {
                $query->where('statut', 'EN_DIFFUSION')
                    ->where('prochaines_seances', 'elemMatch', [
                        'date_heure_debut' => [
                            '$gte' => $upcomingStart->toISOString(),
                            '$lte' => $upcomingEnd->endOfDay()->toISOString(),
                        ],
                    ]);
            })
            ->orderBy('date_sortie', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les films avec séances disponibles via jointure
     */
    private function getFilmsWithAvailableSeances(int $limit)
    {
        // Récupérer les films ayant des séances futures
        $filmIds = SeancePublic::where('date_heure_debut', '>=', now())
            ->where('statut', 'CONFIRMEE')
            ->distinct('film_id')
            ->pluck('film_id');

        return FilmCatalogue::whereIn('film_id', $filmIds)
            ->where('est_actif', true)
            ->orderBy('note_moyenne', 'desc')
            ->limit($limit)
            ->get();
    }
}
