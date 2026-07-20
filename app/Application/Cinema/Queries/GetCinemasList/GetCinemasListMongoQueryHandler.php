<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemasList;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\CinemaListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Infrastructure\Database\ReadModels\CinemaPublic;

/**
 * Handler MongoDB pour la liste des cinémas
 * Utilise Eloquent MongoDB pour des requêtes optimisées read-side
 */
final class GetCinemasListMongoQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetCinemasListQuery);

        try {
            // Utiliser le modèle Eloquent MongoDB
            $mongoQuery = CinemaPublic::query()
                ->actif() // Scope pour cinémas actifs seulement
                ->byLocation($query->location); // Scope pour filtrer par location

            // Appliquer les filtres additionnels si présents
            if ($query->filters) {
                foreach ($query->filters as $field => $value) {
                    $mongoQuery->where($field, $value);
                }
            }

            // Pagination avec Eloquent MongoDB
            $paginator = $mongoQuery
                ->orderBy('nom')
                ->paginate(
                    perPage: $query->perPage,
                    page: $query->page
                );

            // Mapper les modèles MongoDB vers DTOs
            $cinemasDtos = $paginator->map(function (CinemaPublic $cinema) {
                return $this->mapModelToDto($cinema);
            })->toArray();

            // Créer la réponse paginée
            $response = new GetCinemasListQueryResponse(
                cinemas: $cinemasDtos,
                total: $paginator->total(),
                page: $paginator->currentPage(),
                perPage: $paginator->perPage()
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'MONGO_QUERY_FAILED',
                'Erreur lors de la récupération depuis MongoDB: ' . $e->getMessage()
            );
        }
    }

    /**
     * Mapper le modèle Eloquent MongoDB vers DTO
     */
    private function mapModelToDto(CinemaPublic $cinema): CinemaListItemDto
    {
        return new CinemaListItemDto(
            uuid: $cinema->cinema_id,
            nom: $cinema->nom,
            adresse: $cinema->adresse,
            ville: $cinema->ville,
            codePostal: $cinema->code_postal,
            telephone: $cinema->telephone,
            email: $cinema->email,
            nombreSalles: $cinema->nombre_salles,
            horairesOuverture: $cinema->getFormattedHoraires(),
            accessibilitePmr: $cinema->hasAccessibilityPmr(),
            latitude: null,
            longitude: null,
        );
    }
}
