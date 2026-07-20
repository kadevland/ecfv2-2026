<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use Exception;
use ValueError;
use App\Application\Contracts\Result;
use App\Domain\Enums\StatutReservation;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Application\Reservations\DTOs\ReservationListItemDto;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Application\Reservations\Queries\GetReservationsQuery;
use App\Domain\User\Repositories\UserProfilRepositoryInterface;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;
// Modèles Eloquent pour récupération directe (démo)
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;
use App\Infrastructure\Database\Models\Reservations\Reservation as ReservationModel;

final readonly class GetReservationsHandler implements QueryHandlerInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private UserProfilRepositoryInterface $userProfilRepository,
        private SeanceRepositoryInterface $seanceRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetReservationsQuery);

        try {
            $filters = $this->buildFilters($query);

            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                sortBy: $query->sortBy,
                sortDirection: $query->sortDirection,
                filters: $filters,
            );

            $paginatedCollection = $this->reservationRepository->findWithPagination($criteria);

            //dd($paginatedCollection);
            // Enrichir avec les données utilisateur pour l'admin
            $enrichedItems  = [];
            $userIds        = [];
            $reservationIds = [];
            $seanceIds      = [];

            // Collecter tous les IDs pour éviter N+1
            foreach ($paginatedCollection->items as $reservation) {
                $userIds[]        = is_object($reservation->userId) ? $reservation->userId->value : $reservation->userId;
                $reservationIds[] = is_object($reservation->id) ? $reservation->id->value : $reservation->id;
                $seanceIds[]      = is_object($reservation->seanceId) ? $reservation->seanceId->value : $reservation->seanceId;
            }

            // Utiliser les repositories pour récupérer les données
            $userProfils       = $this->userProfilRepository->findByIds($userIds);
            $reservationModels = $this->reservationRepository->findByIds($reservationIds);
            $seances           = $this->seanceRepository->findByIdsWithRelations(array_unique($seanceIds));

            // Créer les DTOs enrichis
            foreach ($paginatedCollection->items as $reservation) {
                $userId        = is_object($reservation->userId) ? $reservation->userId->value : $reservation->userId;
                $reservationId = is_object($reservation->id) ? $reservation->id->value : $reservation->id;
                $seanceId      = is_object($reservation->seanceId) ? $reservation->seanceId->value : $reservation->seanceId;

                $userProfil       = $userProfils[$userId] ?? null;
                $reservationModel = $reservationModels[$reservationId] ?? null;
                $seance           = $seances[$seanceId] ?? null;

                // Extraire les données réelles des entités récupérées
                // Si les relations Domain ne sont pas chargées, utiliser Eloquent en backup
                if (!$seance?->film && $seance) {
                    $film = FilmModel::find($seance->film_db_id);
                    $salle = SalleModel::with('cinema')->find($seance->salle_db_id);
                    $cinema = $salle?->cinema;

                    $filmTitre  = $film ? $film->titre : 'Film inconnu';
                    $salleName  = $salle ? $salle->nom : 'Salle inconnue';
                    $cinemaName = $cinema ? $cinema->nom : 'Cinéma inconnu';
                } else {
                    $filmTitre  = $seance?->film?->titre ?? 'Film inconnu';
                    $salleName  = $seance?->salle?->nom ?? 'Salle inconnue';
                    $cinemaName = $seance?->salle?->cinema?->nom ?? 'Cinéma inconnu';
                }
                $seanceDate = $seance?->dateHeureDebut ?? new DateTime;

                // Données utilisateur réelles
                $userEmail  = $userProfil?->email ?? 'Email non disponible';
                $userNom    = $userProfil?->nom ?? 'Nom non disponible';
                $userPrenom = $userProfil?->prenom ?? 'Prénom non disponible';

                // Générer le label du statut à partir de l'enum avec mapping
                $statutLabel = 'Statut inconnu';
                try {
                    // Mapper les valeurs de la BD vers les valeurs de l'enum (case insensitive)
                    $statutMapped = match (strtolower($reservation->statut)) {
                        'en_attente_paiement' => 'en_attente',
                        'confirmee'           => 'confirmee',
                        'payee'               => 'payee',
                        'annulee'             => 'annulee',
                        'expiree'             => 'expiree',
                        'utilisee'            => 'utilisee',
                        default               => $reservation->statut // garder la valeur originale si déjà correcte
                    };

                    $statutEnum  = StatutReservation::from($statutMapped);
                    $statutLabel = $statutEnum->label();
                } catch (ValueError $e) {
                    // Si le statut n'est pas valide, utiliser la valeur par défaut
                    $statutLabel = match (strtolower($reservation->statut)) {
                        'en_attente_paiement' => 'En attente de paiement',
                        'confirmee'           => 'Confirmée',
                        'payee'               => 'Payée',
                        'annulee'             => 'Annulée',
                        'expiree'             => 'Expirée',
                        'utilisee'            => 'Utilisée',
                        default               => 'Statut inconnu'
                    };
                }

                $model=ReservationModel::where('uuid',$reservationId)->first();

                $enrichedItems[] = new ReservationListItemDto(
                    id: $reservationId,
                    numeroReservation: $reservation->numeroReservation,
                    userId: $userId,
                    userEmail: $userEmail,
                    userNom: $userNom,
                    userPrenom: $userPrenom,
                    seanceId: $seanceId,
                    filmTitre: $filmTitre,
                    seanceDate: $seanceDate,
                    cinemaName: $cinemaName,
                    salleName: $salleName,
                    nombrePlaces: $reservation->nombrePlaces,
                    placesDetails: $reservation->placesDetails,
                    montantTotal: $reservation->montantTotal,
                    montantHt: $reservation->montantHt,
                    statut: $reservation->statut,
                    statutLabel: $statutLabel,
                    dateExpiration: $reservation->dateExpiration,
                    commentaires: $reservation->commentaires,
                    createdAt: $model->created_at,
                    updatedAt: $model->updated_at,
                );
            }

            // Créer une nouvelle collection paginée avec les DTOs
            $enrichedCollection = new \App\Domain\Shared\ValueObjects\PaginatedCollection(
                items: $enrichedItems,
                total: $paginatedCollection->total,
                criteria: $paginatedCollection->criteria
            );

            return Result::success($enrichedCollection);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des réservations: ' . $e->getMessage()
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFilters(GetReservationsQuery $query): array
    {
        $filters = [];

        if ($query->userId !== null) {
            $filters['user_id'] = $query->userId;
        }

        if ($query->seanceId !== null) {
            $filters['seance_id'] = $query->seanceId;
        }

        if ($query->statut !== null) {
            // Mapper les valeurs de l'enum vers les valeurs de la BD pour le filtre
            $statutMappedForFilter = match ($query->statut) {
                'en_attente' => 'EN_ATTENTE_PAIEMENT',
                'confirmee'  => 'CONFIRMEE',
                'payee'      => 'PAYEE',
                'annulee'    => 'ANNULEE',
                'expiree'    => 'EXPIREE',
                'utilisee'   => 'UTILISEE',
                default      => $query->statut
            };
            $filters['statut'] = $statutMappedForFilter;
        }

        if ($query->numeroReservation !== null) {
            $filters['numero_reservation'] = $query->numeroReservation;
        }

        if ($query->dateFrom !== null) {
            $filters['date_from'] = $query->dateFrom;
        }

        if ($query->dateTo !== null) {
            $filters['date_to'] = $query->dateTo;
        }

        return $filters;
    }
}
