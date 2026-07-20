<?php

declare(strict_types=1);

namespace App\Application\Seance\Queries\GetSeancesList;

use Exception;
use App\Domain\Enums\StatutSeance;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Seance;
use App\Application\Contracts\QueryInterface;
use App\Application\Seance\DTOs\SeanceListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\Cinema\Repositories\ReservationRepositoryInterface;

final class GetSeancesListQueryHandler implements QueryHandlerInterface
{
    public function __construct (
        private readonly SeanceRepositoryInterface $seanceRepository,
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
        private readonly ReservationRepositoryInterface $reservationRepository
    ) {}

    public function handle (QueryInterface $query) : Result
    {
        if (!$query instanceof GetSeancesListQuery) {
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

            if ($query->filmUuid) {
                $filters['film_id'] = $query->filmUuid;
            }

            if ($query->salleUuid) {
                $filters['salle_id'] = $query->salleUuid;
            }

            if ($query->cinemaUuid) {
                $filters['cinema_id'] = $query->cinemaUuid;
            }

            if ($query->dateDebut) {
                $filters['date_debut'] = $query->dateDebut;
                $filters['date_fin']   = $query->dateDebut;
            }

            if ($query->dateFin) {
                $filters['date_fin'] = $query->dateFin;
            }

            if ($query->version) {
                $filters['version'] = $query->version;
            }

            if ($query->technologies) {
                $filters['technologies'] = $query->technologies;
            }

            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            if ($query->seancesAVenir !== null) {
                $filters['seances_a_venir'] = $query->seancesAVenir;
            }

            if ($query->filters) {
                $filters = array_merge($filters, $query->filters);
            }

            // Ajouter les critères de tri
            $filters['sort_by']        = $query->sortBy;
            $filters['sort_direction'] = $query->sortDirection;

            //dump($filters);


            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters,
                sortBy: $query->sortBy ?? 'date_heure_debut',
                sortDirection: $query->sortDirection ?? 'desc'
            );
            //dump($criteria);C
            // Utiliser le repository avec la pagination
            $paginatedCollection = $this->seanceRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs avec eager loading optimisé
            $seancesDtos = $this->mapToDtosWithEagerLoading($paginatedCollection->items);

            // Créer la réponse paginée
            $response = new GetSeancesListQueryResponse(
                seances: $seancesDtos,
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

    /**
     * Mapper les séances vers DTOs avec eager loading optimisé
     *
     * @param array<Seance> $seances
     * @return array<SeanceListItemDto>
     */
    private function mapToDtosWithEagerLoading (array $seances) : array
    {

        if (empty($seances)) {
            return [];
        }

        // Collecter tous les IDs uniques
        $filmIdsValues   = [];
        $salleIdsValues  = [];
        $seanceIdsValues = [];

        foreach ($seances as $seance) {
            $filmIdsValues[$seance->filmId->value]   = $seance->filmId;
            $salleIdsValues[$seance->salleId->value] = $seance->salleId;
            $seanceIdsValues[$seance->id->value]     = $seance->id;
        }

        // Les IDs uniques
        $filmIds   = array_values($filmIdsValues);
        $salleIds  = array_values($salleIdsValues);
        $seanceIds = array_values($seanceIdsValues);

        // Batch loading avec findByIds
        $films                = $this->filmRepository->findByIds($filmIds);
        $salles               = $this->salleRepository->findByIds($salleIds);
        $reservationsBySeance = $this->reservationRepository->findBySeanceIds($seanceIds);
        // Collecter les IDs des cinémas depuis les salles

        foreach ($salles as $salle) {
            $cinemaIdsValues[$salle->cinemaId->value] = $salle->cinemaId;
        }
        $cinemaIds = array_values($cinemaIdsValues);
        $cinemas   = $this->cinemaRepository->findByIds($cinemaIds);

        // Mapper chaque séance vers DTO
        $dtos = [];
        foreach ($seances as $seance) {
            $dtos[] = $this->mapToDtoWithPreloadedData(
                $seance,
                $films,
                $salles,
                $cinemas,
                $reservationsBySeance
            );
        }

        return $dtos;
    }

    /**
     * @param array<string, mixed> $films
     * @param array<string, mixed> $salles
     * @param array<string, mixed> $cinemas
     * @param array<string, mixed> $reservationsBySeance
     */
    private function mapToDtoWithPreloadedData (
        Seance $seance,
        array $films,
        array $salles,
        array $cinemas,
        array $reservationsBySeance
    ) : SeanceListItemDto {
        // Récupérer les données préchargées
        $film   = $films[$seance->filmId->value] ?? null;
        $salle  = $salles[$seance->salleId->value] ?? null;
        $cinema = null;
        if ($salle) {
            $cinema = $cinemas[$salle->cinemaId->value] ?? null;
        }

        // Calculer les places disponibles
        $reservations = $reservationsBySeance[$seance->id->value] ?? [];

        $placesReservees = array_sum(array_map(fn ($reservation) => $reservation->nombrePlaces, $reservations));


        $placesTotales     = $salle->capaciteTotale ?? 0;
        $placesDisponibles = max(0, $placesTotales - $placesReservees);

        // Construire le nom d'affichage
        $cinemaNom        = $cinema->nom ?? 'Cinéma N/A';
        $salleNom         = $salle->nom ?? 'Salle N/A';
        $salleDisplayName = $cinemaNom . ' - ' . $salleNom;

        return new SeanceListItemDto(
            uuid: $seance->id->value,
            dateHeure: $seance->dateHeureDebut->format('Y-m-d H:i:s'),
            dateHeureDebut: $seance->dateHeureDebut->format('Y-m-d H:i:s'),
            dateHeureFin: $seance->dateHeureFin->format('Y-m-d H:i:s'),
            filmTitre: $film->titre ?? 'Film N/A',
            filmUuid: $seance->filmId->value,
            salleNom: $salleNom,
            salleUuid: $seance->salleId->value,
            salleNumero: $salle->numero ?? 0,
            cinemaNom: $cinemaNom,
            cinemaUuid: $cinema?->id->value ?? '',
            salleDisplayName: $salleDisplayName,
            version: $seance->version,
            // @phpstan-ignore property.notFound
            technologies: $seance->optionsSupplementaires ?? [],
            // @phpstan-ignore nullCoalesce.expr
            prixMin: $seance->getTarification()
                ->getPrixMinimum()
                    ?->getAmount() / 100 ?? 0,
            // @phpstan-ignore nullCoalesce.expr
            prixMax: $seance->getTarification()
                ->getPrixMaximum()
                    ?->getAmount() / 100 ?? 0,
            placesDisponibles: $placesDisponibles,
            placesTotales: $placesTotales,
            statut: $seance->statut->value,
            estComplete: $placesDisponibles === 0,
            estAnnulee: $seance->statut === StatutSeance::ANNULEE,
        );
    }
}
