<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemasList;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\CinemaListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class GetCinemasListQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository,
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetCinemasListQuery);

        try {
            // Préparer les filtres
            $filters = [];

            if ($query->location) {
                $filters['location'] = $query->location;
            }

            if ($query->filters) {
                $filters = array_merge($filters, $query->filters);
            }

            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters
            );

            // Utiliser le repository avec la nouvelle signature
            $paginatedCollection = $this->cinemaRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs
            $cinemasDtos = array_map(
                fn (Cinema $cinema) => $this->mapToDto($cinema),
                $paginatedCollection->items
            );

            // Créer la réponse paginée
            $response = new GetCinemasListQueryResponse(
                cinemas: $cinemasDtos,
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

    private function mapToDto(Cinema $cinema): CinemaListItemDto
    {
        // Compter les salles du cinéma
        $nombreSalles = $this->salleRepository->countByCinema($cinema->id);

        // Horaires par défaut - TODO: À terme, implémenter HorairesOuverture ValueObject
        $horairesOuverture = [
            'aujourd_hui' => '09:00-22:00',
            'status'      => 'ouvert', // ou 'fermé' selon l'heure actuelle
        ];

        // Calculer l'accessibilité PMR (optimisé pour liste - pas de chargement de salles détaillées)
        $accessibilitePmr = $this->hasAccessibilityPmr($cinema->id);

        return new CinemaListItemDto(
            uuid: $cinema->id->value,
            nom: $cinema->nom,
            adresse: $cinema->adresse->rue,
            ville: $cinema->adresse->ville,
            codePostal: $cinema->adresse->codePostal,
            telephone: $cinema->telephone?->telephoneInternational,
            email: $cinema->email?->value,
            nombreSalles: $nombreSalles,
            horairesOuverture: $horairesOuverture,
            accessibilitePmr: $accessibilitePmr,
            latitude: $cinema->coordonneesGps?->latitude,
            longitude: $cinema->coordonneesGps?->longitude,
        );
    }

    /**
     * Vérification optimisée de l'accessibilité PMR pour la liste (sans charger toutes les salles)
     */
    private function hasAccessibilityPmr(CinemaId $cinemaId): bool
    {
        // Utiliser la requête optimisée qui fait directement query()->count() > 0
        // au lieu de charger des entités complètes puis les filtrer
        return $this->salleRepository->hasAccessibleRoomByCinema($cinemaId);
    }
}
