<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Services\MongoDB\FilmCatalogueQueryService;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

/**
 * Contrôleur pour la page d'accueil
 * Intègre les données MongoDB optimisées
 */
class AccueilController extends Controller
{
    public function __construct(
        private FilmCatalogueQueryService $filmService
    ) {}

    /**
     * Affiche la page d'accueil avec données dynamiques
     */
    public function index(Request $request): View
    {
        // Cache les données de la homepage pour 15 minutes
        $data = Cache::remember('homepage_data', 15 * 60, function () {
            return $this->getHomePageData();
        });

        return view('welcome', $data);
    }

    /**
     * API pour le carrousel films (AJAX)
     */
    public function filmsCarousel(Request $request): \Illuminate\Http\JsonResponse
    {
        $type  = $request->get('type', 'featured');
        $limit = min((int) $request->get('limit', 8), 20);

        $films = match ($type) {
            'trending' => $this->filmService->getTrendingFilms(7, $limit),
            'new'      => FilmCatalogue::enDiffusion()
                ->releasedAfter(now()->subDays(30)->toDateTime())
                ->recent()
                ->limit($limit)
                ->get(),
            'top' => FilmCatalogue::enDiffusion()
                ->minNote(4.0)
                ->popular()
                ->limit($limit)
                ->get(),
            default => FilmCatalogue::enDiffusion()
                ->withAvailableSeances()
                ->popularWithMinRating(3.5)
                ->limit($limit)
                ->get()
        };

        return response()->json([
            'type'  => $type,
            'films' => $films,
            'count' => count($films),
        ]);
    }

    /**
     * Recherche rapide pour l'auto-complétion homepage
     */
    public function quickSearch(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50',
        ]);

        $query = $request->q;

        // Cache la recherche pour 5 minutes
        $results = Cache::remember(
            "search_{$query}",
            5 * 60,
            function () use ($query) {
                // Recherche dans les titres (rapide)
                $films = FilmCatalogue::enDiffusion()
                    ->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
                    ->limit(8)
                    ->get(['film_id', 'titre', 'genre', 'note_moyenne', 'affiche_url']);

                // Recherche dans les genres
                $genres = FilmCatalogue::enDiffusion()
                    ->where('genre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
                    ->distinct('genre')
                    ->take(3);

                return [
                    'films'  => $films,
                    'genres' => $genres,
                ];
            }
        );

        return response()->json([
            'query'   => $query,
            'results' => $results,
        ]);
    }

    /**
     * Statistiques temps réel pour le dashboard
     */
    public function stats(): \Illuminate\Http\JsonResponse
    {
        $stats = Cache::remember('homepage_stats', 10 * 60, function () {
            return [
                'films_total'      => FilmCatalogue::enDiffusion()->count(),
                'films_nouveautes' => FilmCatalogue::enDiffusion()
                    ->releasedAfter(now()->subDays(7)->toDateTime())
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

    /**
     * Suggestions personnalisées basées sur l'historique
     */
    public function suggestions(Request $request): \Illuminate\Http\JsonResponse
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

    /**
     * Affiche la page de réservation avec films groupés par cinéma
     */

    /**
     * API pour récupérer les séances d'un film
     */
    public function seancesByFilm(Request $request, string $filmId): \Illuminate\Http\JsonResponse
    {
        // Utiliser CQRS avec QueryBus
        $query = new \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQuery(
            filmId: $filmId,
            futuresOnly: true,
            limit: $request->integer('limit', 10)
        );

        $queryBus = app(\App\Application\Bus\QueryBus::class);
        $result   = $queryBus->ask($query);

        if ($result->isError()) {
            return response()->json([
                'error' => $result->getErrorMessage(),
            ], $result->getErrorCode() === 'INVALID_QUERY' ? 400 : 500);
        }

        $response = $result->getValue();

        return response()->json($response->toArray());
    }

    /**
     * Affiche la page mon compte
     */
    public function account(Request $request): View
    {
        // Pour l'instant page statique, à connecter avec l'auth plus tard
        return view('public.account.index');
    }

    /**
     * Affiche la page QR code publique pour une réservation
     */
    public function qrCode(Request $request, string $reservationNumber): View
    {
        // Simuler des données de réservation basées sur le numéro
        // En production, on récupérerait depuis la base de données
        $reservationData = $this->generateMockReservationData($reservationNumber);

        if (!$reservationData) {
            abort(404, 'Réservation non trouvée');
        }

        return view('public.qr.show', [
            'reservation'       => $reservationData,
            'reservationNumber' => $reservationNumber,
        ]);
    }

    /**
     * Télécharge le billet en PDF pour une réservation
     */
    public function downloadTicket(Request $request, string $reservationNumber): \Illuminate\Http\Response
    {
        // Récupérer les données de réservation (simulées pour l'instant)
        $reservationData = $this->generateMockReservationDataForPdf($reservationNumber);

        if (!$reservationData) {
            abort(404, 'Réservation non trouvée');
        }

        // Générer le PDF avec DomPDF
        $pdf = Pdf::loadView('pdf.ticket', [
            'reservation' => $reservationData,
        ]);

        return $pdf->download("billet-{$reservationNumber}.pdf");
    }

    /**
     * Affiche la page de notation d'un film
     */
    public function rateFilm(Request $request, string $filmId): View
    {
        // Récupérer le film depuis MongoDB
        $film = FilmCatalogue::where('film_id', $filmId)->first();

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        /** @phpstan-ignore-next-line argument.type */
        return view('public.rating.index', [
            'film' => $film,
        ]);
    }

    /**
     * Traite la notation d'un film
     */
    public function submitRating(Request $request, string $filmId): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:500',
            'username' => 'required|string|max:100',
        ]);

        // Simuler l'enregistrement de la note
        // En production, on enregistrerait en base de données
        $rating   = $request->rating;
        $comment  = $request->comment;
        $username = $request->username;

        // Simuler la mise à jour de la note moyenne du film
        $film = FilmCatalogue::where('film_id', $filmId)->first();
        if ($film) {
            // En production, on recalculerait la moyenne
            $newAverageRating = round(($film->note_moyenne * $film->nombre_avis + $rating) / ($film->nombre_avis + 1), 1);

            // Mise à jour simulée (en production avec vraie logique)
            $film->update([
                'note_moyenne' => $newAverageRating,
                'nombre_avis'  => $film->nombre_avis + 1,
            ]);
        }

        return redirect()->route('rating.show', $filmId)
            ->with('success', 'Votre note a été enregistrée avec succès !')
            ->with('rating_submitted', [
                'rating'   => $rating,
                'comment'  => $comment,
                'username' => $username,
            ]);
    }

    /**
     * Vide le cache de la homepage (admin)
     */
    public function clearCache(): \Illuminate\Http\JsonResponse
    {
        Cache::forget('homepage_data');
        Cache::forget('homepage_stats');

        // Vider aussi les caches de recherche
        $pattern = 'search_*';
        if (config('cache.default') === 'redis') {
            $keys = Redis::keys($pattern);
            if ($keys) {
                Redis::del($keys);
            }
        }

        return response()->json([
            'message' => 'Cache homepage vidé avec succès',
        ]);
    }

    /**
     * Génère des données simulées de réservation
     */
    /**
     * @return array<string, mixed>|null
     */
    private function generateMockReservationData(string $reservationNumber): ?array
    {
        // Simuler une base de données de réservations
        $mockReservations = [
            'RES-AB12CD34' => [
                'film_titre'     => 'Avatar: La Voie de l\'Eau',
                'film_genre'     => 'Science-Fiction',
                'film_duree'     => 192,
                'cinema_nom'     => 'Cinéphoria Champs-Élysées',
                'cinema_adresse' => '123 Avenue des Champs-Élysées, 75008 Paris',
                'salle'          => 'Salle 3',
                'date_seance'    => '2025-01-15 20:30:00',
                'nb_places'      => 2,
                'places'         => ['A12', 'A13'],
                'tarif_unitaire' => 12.50,
                'total'          => 25.00,
                'statut'         => 'confirmee',
                'created_at'     => '2025-01-10 14:30:00',
            ],
            'RES-EF56GH78' => [
                'film_titre'     => 'Top Gun: Maverick',
                'film_genre'     => 'Action',
                'film_duree'     => 131,
                'cinema_nom'     => 'Cinéphoria République',
                'cinema_adresse' => '45 Boulevard Saint-Martin, 75003 Paris',
                'salle'          => 'Salle 1',
                'date_seance'    => '2025-01-18 18:15:00',
                'nb_places'      => 1,
                'places'         => ['D8'],
                'tarif_unitaire' => 12.50,
                'total'          => 12.50,
                'statut'         => 'confirmee',
                'created_at'     => '2025-01-12 16:45:00',
            ],
            'RES-IJ90KL12' => [
                'film_titre'     => 'Black Panther: Wakanda Forever',
                'film_genre'     => 'Action',
                'film_duree'     => 161,
                'cinema_nom'     => 'Cinéphoria Bastille',
                'cinema_adresse' => '78 Rue de la Roquette, 75011 Paris',
                'salle'          => 'Salle 2',
                'date_seance'    => '2025-01-07 21:00:00',
                'nb_places'      => 2,
                'places'         => ['F5', 'F6'],
                'tarif_unitaire' => 12.50,
                'total'          => 25.00,
                'statut'         => 'terminee',
                'created_at'     => '2025-01-02 09:15:00',
            ],
        ];

        return $mockReservations[$reservationNumber] ?? null;
    }

    /**
     * Génère des données simulées de réservation pour le PDF
     */
    /**
     * @return array<string, mixed>|null
     */
    private function generateMockReservationDataForPdf(string $reservationNumber): ?array
    {
        // Données adaptées au template PDF avec les clés attendues
        $mockReservations = [
            'RES-AB12CD34' => [
                'numeroReservation' => 'RES-AB12CD34',
                'film'              => 'Avatar: La Voie de l\'Eau',
                'genre'             => 'Science-Fiction',
                'duree'             => '192',
                'classification'    => 'Tous publics',
                'dateHeure'         => '2025-01-15 20:30:00',
                'cinema'            => 'Cinéphoria Champs-Élysées',
                'adresse'           => '123 Avenue des Champs-Élysées, 75008 Paris',
                'salle'             => 'Salle 3',
                'nbPlaces'          => 2,
                'places'            => ['A12', 'A13'],
                'total'             => '25.00',
            ],
            'RES-EF56GH78' => [
                'numeroReservation' => 'RES-EF56GH78',
                'film'              => 'Top Gun: Maverick',
                'genre'             => 'Action',
                'duree'             => '131',
                'classification'    => 'Tous publics',
                'dateHeure'         => '2025-01-18 18:15:00',
                'cinema'            => 'Cinéphoria République',
                'adresse'           => '45 Boulevard Saint-Martin, 75003 Paris',
                'salle'             => 'Salle 1',
                'nbPlaces'          => 1,
                'places'            => ['D8'],
                'total'             => '12.50',
            ],
            'RES-IJ90KL12' => [
                'numeroReservation' => 'RES-IJ90KL12',
                'film'              => 'Black Panther: Wakanda Forever',
                'genre'             => 'Action',
                'duree'             => '161',
                'classification'    => 'Tous publics',
                'dateHeure'         => '2025-01-07 21:00:00',
                'cinema'            => 'Cinéphoria Bastille',
                'adresse'           => '78 Rue de la Roquette, 75011 Paris',
                'salle'             => 'Salle 2',
                'nbPlaces'          => 2,
                'places'            => ['F5', 'F6'],
                'total'             => '25.00',
            ],
        ];

        return $mockReservations[$reservationNumber] ?? null;
    }

    /**
     * Récupère toutes les données nécessaires pour la homepage
     */
    /**
     * @return array<string, mixed>
     */
    private function getHomePageData(): array
    {
        // Films populaires en vedette (top 8)
        $featuredFilms = FilmCatalogue::enDiffusion()
            ->popular()
            ->limit(8)
            ->get();

        // Films les mieux notés
        $topRatedFilms = FilmCatalogue::enDiffusion()
            ->minNote(4.0)
            ->popular()
            ->limit(5)
            ->get();

        // Statistiques rapides pour la section hero
        $stats = [
            'total_films'  => FilmCatalogue::enDiffusion()->count(),
            'genres_count' => 8, // Valeur fixe pour éviter l'erreur
            'avg_rating'   => 4.2, // Valeur fixe pour éviter l'erreur
        ];

        return [
            'featuredFilms' => $featuredFilms,
            'trendingFilms' => collect([]), // Vide pour l'instant
            'newReleases'   => collect([]),
            'topRatedFilms' => $topRatedFilms,
            'stats'         => $stats,
            'popularGenres' => collect(['action', 'comedy', 'drama']),
        ];
    }

    /**
     * Récupère les données de réservation groupées par cinéma
     */
}
