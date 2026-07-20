<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Public\Repositories\SearchRepositoryInterface;

class SearchController extends Controller
{
    public function __construct(
        protected SearchRepositoryInterface $searchRepository
    ) {}

    /**
     * Recherche HTMX instantanée de films
     */
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $request->validate([
            'q' => 'nullable|string|max:50',
        ]);

        $query = trim($request->get('q', ''));

        // Déterminer quelle vue utiliser (modal vs dropdown)
        $isModal  = $request->header('HX-Target') === 'modal-search-results';
        $viewName = $isModal ? 'public.search.modal-results' : 'public.search.results';

        // Si query vide, retourner vide
        if (empty($query) || strlen($query) < 2) {
            return view($viewName, [
                'films'      => collect([]),
                'query'      => $query,
                'hasResults' => false,
            ]);
        }

        // Recherche via repository
        $films = $this->searchRepository->instantSearch($query, 6)
            ->map(function ($film) {
                return (object) [
                    'film_id'       => $film->film_id,
                    'titre'         => $film->titre,
                    'affiche_url'   => $film->affiche_url,
                    'note_moyenne'  => $film->note_moyenne ?? 0,
                    'duree_minutes' => $film->duree_minutes,
                    'genre'         => is_array($film->genres ?? []) && !empty($film->genres)
                             ? $film->genres[0] : 'Non classé',
                    'classification' => $film->classification,
                    'synopsis'       => $film->synopsis ? Str::limit($film->synopsis, 100) : null,
                ];
            });

        return view($viewName, [
            'films'      => $films,
            'query'      => $query,
            'hasResults' => $films->isNotEmpty(),
        ]);
    }
}
