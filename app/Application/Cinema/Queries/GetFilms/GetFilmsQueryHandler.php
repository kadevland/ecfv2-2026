<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetFilms;

use Exception;
use App\Domain\Cinema\Entities\Film;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\FilmListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;

final class GetFilmsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FilmRepositoryInterface $filmRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetFilmsQuery);

        try {
            // Préparer les filtres
            $filters = [];

            if ($query->genre) {
                $filters['genre'] = $query->genre;
            }

            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            if ($query->estActif !== null) {
                $filters['est_actif'] = $query->estActif;
            }

            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters,
                sortBy: $query->sortBy,
                sortDirection: $query->sortDirection,
            );

            // Utiliser le repository
            $paginatedCollection = $this->filmRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs
            $filmsDtos = array_map(
                fn (Film $film) => $this->mapToDto($film),
                $paginatedCollection->items
            );

            // Créer la réponse paginée
            $response = new GetFilmsQueryResponse(
                films: $filmsDtos,
                total: $paginatedCollection->total,
                page: $paginatedCollection->currentPage(),
                perPage: $paginatedCollection->perPage()
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des films: ' . $e->getMessage()
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
            acteursPrincipaux: $film->acteursPrincipaux,
            genres: $film->genres,
            dureeMinutes: $film->dureeMinutes,
            classification: $film->classification,
            langueOriginale: $film->langueOriginale,
            sousTitres: $film->sousTitres,
            resume: $film->synopsis,
            dateSortie: $film->dateSortie->format('Y-m-d'),
            dateFinExploitation: $film->dateFinExploitation?->format('Y-m-d'),
            notePresse: $film->noteCritique,
            notePublic: $film->notePublic,
            noteMoyenneAvis: $film->noteMoyenneAvis,
            nombreAvis: $film->nombreAvis,
            afficheUrl: $film->afficheUrl,
            bandeAnnonceUrl: $film->bandeAnnonceUrl,
            estActif: $film->estActif,
        );
    }
}
