<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Public\Repositories\SearchRepositoryInterface;

class ModalSearchController extends Controller
{
    public function __construct(
        protected SearchRepositoryInterface $searchRepository
    ) {}

    /**
     * Recherche pour la modal - Fragment HTMX
     */
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $query = trim($request->get('q', ''));

        // Si query vide ou trop courte, retourner état initial
        if (strlen($query) < 2) {
            return view('fragments.modal-search-empty');
        }

        // Recherche via repository
        $films = $this->searchRepository->quickSearch($query, 8)
            ->map(function ($film) {
                // Parse JSON fields pour compatibilité avec la vue
                $realisateurs = is_string($film->realisateurs ?? '')
                    ? json_decode($film->realisateurs, true) ?? []
                    : ($film->realisateurs ?? []);

                $acteurs = is_string($film->acteurs_principaux ?? '')
                    ? json_decode($film->acteurs_principaux, true) ?? []
                    : ($film->acteurs_principaux ?? []);

                $genres = is_string($film->genres ?? '')
                    ? json_decode($film->genres, true) ?? []
                    : ($film->genres ?? []);

                return (object) [
                    'film_id'       => $film->film_id ?? $film->_id,
                    'titre'         => $film->titre,
                    'affiche_url'   => $film->affiche_url,
                    'duree_minutes' => $film->duree_minutes,
                    'genre'         => !empty($genres) ? $genres[0] : null,
                    'note_moyenne'  => $film->note_moyenne ?? 0,
                    'realisateur'   => !empty($realisateurs) ? $realisateurs[0] : null,
                    'acteurs'       => array_slice($acteurs, 0, 2), // 2 premiers acteurs
                ];
            });

        return view('fragments.modal-search-results', [
            'films'      => $films,
            'query'      => $query,
            'hasResults' => $films->isNotEmpty(),
        ]);
    }
}
