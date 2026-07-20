<?php

declare(strict_types=1);

namespace App\Application\Film\Queries\GetFilmsList;

use Exception;
use App\Domain\Cinema\Entities\Film;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Film\DTOs\FilmListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;

final class GetFilmsListQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FilmRepositoryInterface $filmRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetFilmsListQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Paramètres de requête invalides'
                );
            }

            // Préparer les filtres
            $filters = [];

            if ($query->search) {
                $filters['search'] = $query->search;
            }

            if ($query->genres) {
                $filters['genres'] = $query->genres;
            }

            if ($query->classification) {
                $filters['classification'] = $query->classification;
            }

            if ($query->enSalles !== null) {
                $filters['en_salles'] = $query->enSalles;
            }

            if ($query->prochainement !== null) {
                $filters['prochainement'] = $query->prochainement;
            }

            if ($query->filters) {
                $filters = array_merge($filters, $query->filters);
            }

            // Ajouter les critères de tri
            // $filters['sort_by']        = FilmSchema::PRIMARY_KEY;
            // $filters['sort_direction'] = 'desc';
            // $filters['sort_by']        = $query?->sortBy ?? FilmSchema::PRIMARY_KEY ;
            // $filters['sort_direction'] = $query?->sortDirection ?? 'desc';

            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters,
                sortBy: FilmSchema::PRIMARY_KEY,
                sortDirection: 'desc',
            );

            // Utiliser le repository avec la nouvelle signature
            $paginatedCollection = $this->filmRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs
            $filmsDtos = array_map(
                fn (Film $film) => $this->mapToDto($film),
                $paginatedCollection->items
            );

            // Créer la réponse paginée
            $response = new GetFilmsListQueryResponse(
                films: $filmsDtos,
                total: $paginatedCollection->total,
                page: $paginatedCollection->currentPage(),
                perPage: $paginatedCollection->perPage()
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération de la liste: ' . $e->getMessage()
            );
        }
    }

    private function mapToDto(Film $film): FilmListItemDto
    {
        return new FilmListItemDto(
            uuid: $film->id->value,
            titre: $film->titre,
            titreFr: $film->titreOriginal,
            realisateurs: $film->realisateurs,
            genres: $film->genres,
            dureeMinutes: $film->dureeMinutes,
            dureeFormatted: $film->getFormattedDuration(),
            classification: $film->classification,
            dateSortie: $film->dateSortie->format('Y-m-d'),
            dateFinExploitation: $film->dateFinExploitation?->format('Y-m-d'),
            notePresse: $film->noteCritique,
            notePublic: $film->notePublic,
            noteMoyenneAvis: $film->noteMoyenneAvis,
            nombreAvis: $film->nombreAvis,
            afficheUrl: $film->afficheUrl,
            estActif: $film->estActif,
            isInTheaters: $film->isInTheaters(),
        );
    }
}
