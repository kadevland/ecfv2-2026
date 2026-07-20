<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmsCatalog;

use Exception;
use App\Application\Contracts\Result;
use Illuminate\Database\Eloquent\Builder;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

/**
 * Handler MongoDB pour le catalogue public des films
 * Utilise MongoDB pour des performances optimales en lecture (read-side CQRS)
 */
final class GetFilmsCatalogQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetFilmsCatalogQuery);

        try {
            // Builder MongoDB avec scopes
            $builder = FilmCatalogue::query();

            // Appliquer les filtres
            $this->applyFilters($builder, $query);

            // Compter le total avec les filtres appliqués
            $total = $builder->count();

            // Appliquer la pagination et le tri
            $offset = ($query->page - 1) * $query->perPage;

            // Trier les résultats
            $sortDirectionString = strtolower($query->sortDirection) === 'desc' ? 'desc' : 'asc';
            $builder->orderBy($query->sortBy, $sortDirectionString);

            // Paginer
            /** @var array<int, array<string, mixed>> $films */
            $films = $builder->skip($offset)
                ->take($query->perPage ?? 20)
                ->get()
                ->map(function (FilmCatalogue $film) {
                    return [
                        'film_id'               => $film->film_id,
                        'titre'                 => $film->titre,
                        'titre_original'        => $film->titre_original,
                        'realisateurs'          => $film->realisateurs ?? [],
                        'genres'                => $film->genres ?? [],
                        'duree_minutes'         => $film->duree_minutes,
                        'duree_formatee'        => $film->getFormattedDuration(),
                        'classification'        => $film->classification,
                        'date_sortie'           => $film->date_sortie->format('Y-m-d'),
                        'date_fin_exploitation' => $film->date_fin_exploitation?->format('Y-m-d'),
                        'note_critique'         => $film->note_critique,
                        'note_public'           => $film->note_public,
                        'note_moyenne_avis'     => $film->note_moyenne_avis,
                        'nombre_avis'           => $film->nombre_avis,
                        'affiche_url'           => $film->affiche_url,
                        'synopsis'              => $film->synopsis,
                        'statut'                => $film->statut,
                        'est_actif'             => $film->est_actif,
                        'en_exploitation'       => $film->isInTheaters(),
                    ];
                })
                ->toArray();

            $totalPages = intval(ceil($total / ($query->perPage ?? 20)));

            $response = new GetFilmsCatalogQueryResponse(
                films: $films,
                total: $total,
                page: $query->page ?? 1,
                perPage: $query->perPage ?? 20,
                totalPages: $totalPages,
                filters: [
                    'genre'          => $query->genre,
                    'classification' => $query->classification,
                    'search'         => $query->search,
                    'in_theaters'    => $query->inTheaters,
                    'sort_by'        => $query->sortBy,
                    'sort_direction' => $query->sortDirection,
                ]
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'FILMS_CATALOG_QUERY_FAILED',
                'Erreur lors de la récupération du catalogue films depuis MongoDB: ' . $e->getMessage()
            );
        }
    }

    /**
     * @param Builder<FilmCatalogue> $builder
     */
    private function applyFilters(Builder $builder, GetFilmsCatalogQuery $query): void
    {
        // Toujours montrer que les films actifs pour le public
        $builder->where('est_actif', true);

        // Filtre par genre
        if ($query->genre) {
            $builder->where('genres', $query->genre);
        }

        // Filtre par classification
        if ($query->classification) {
            $builder->where('classification', $query->classification);
        }

        // Films en exploitation seulement
        if ($query->inTheaters) {
            $now = now();
            $builder->where('date_sortie', '<=', $now)
                ->where(function ($q) use ($now) {
                    $q->whereNull('date_fin_exploitation')
                        ->orWhere('date_fin_exploitation', '>=', $now);
                });
        }

        // Recherche textuelle (titre, réalisateurs, synopsis)
        if ($query->search) {
            $search = trim($query->search);
            $builder->where(function ($q) use ($search) {
                $q->where('titre', 'regex', new \MongoDB\BSON\Regex($search, 'i'))
                    ->orWhere('titre_original', 'regex', new \MongoDB\BSON\Regex($search, 'i'))
                    ->orWhere('synopsis', 'regex', new \MongoDB\BSON\Regex($search, 'i'))
                    ->orWhere('realisateurs', $search)
                    ->orWhere('acteurs_principaux', $search);
            });
        }
    }
}
