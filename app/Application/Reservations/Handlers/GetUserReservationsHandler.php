<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use Exception;
use App\Application\Contracts\Result;
use App\Domain\User\ValueObjects\UserId;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Reservations\DTOs\UserReservationDto;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Application\Reservations\Queries\GetUserReservationsQuery;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;
// Modèles Eloquent pour récupération directe (démo)
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;

final class GetUserReservationsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly SeanceRepositoryInterface $seanceRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetUserReservationsQuery) {
            return Result::error('INVALID_QUERY', 'Query type not supported');
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Query invalide'
                );
            }

            // Récupérer le UserProfil UUID depuis l'User ID
            $userProfil = UserProfil::where(UserProfilSchema::USER_ID, $query->userId)->first();
            if (!$userProfil) {
                return Result::error(
                    error: 'USER_NOT_FOUND',
                    message: 'Utilisateur introuvable'
                );
            }

            $userUuid = $userProfil->{UserProfilSchema::ID};
            $userId   = new UserId($userUuid);

            // Récupérer les réservations de l'utilisateur
            $filters = [];
            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            $paginatedReservations = $this->reservationRepository->findByUserId(
                userId: $userId,
                filters: $filters,
                page: $query->page,
                perPage: $query->perPage
            );

            // Enrichir avec les données des séances
            $seanceIds = [];
            foreach ($paginatedReservations->items as $reservation) {
                $seanceId    = is_object($reservation->seanceId) ? $reservation->seanceId->value : $reservation->seanceId;
                $seanceIds[] = $seanceId;
            }

            $seances = $this->seanceRepository->findByIdsWithRelations($seanceIds);

            // Créer les DTOs enrichis
            $enrichedItems = [];
            foreach ($paginatedReservations->items as $reservation) {
                $reservationId = is_object($reservation->id) ? $reservation->id->value : $reservation->id;
                $seanceId      = is_object($reservation->seanceId) ? $reservation->seanceId->value : $reservation->seanceId;

                $seance = $seances[$seanceId] ?? null;

                // Récupération via Eloquent comme dans GetReservationDetailHandler
                $film = $seance ? FilmModel::find($seance->film_db_id) : null;
                $salle = $seance ? SalleModel::with('cinema')->find($seance->salle_db_id) : null;
                $cinema = $salle?->cinema;

                $filmTitre  = $film ? $film->titre : 'Film inconnu';
                $seanceDate = $seance?->date_seance ? new DateTime($seance->date_seance) : new DateTime();
                $salleName  = $salle ? $salle->nom : 'Salle inconnue';
                $cinemaName = $cinema ? $cinema->nom : 'Cinéma inconnu';

                $enrichedItems[] = new UserReservationDto(
                    id: $reservationId,
                    numeroReservation: $reservation->numeroReservation,
                    filmTitre: $filmTitre,
                    dateHeureDebut: $seanceDate,
                    cinemaName: $cinemaName,
                    salleNom: $salleName,
                    nombrePlaces: $reservation->nombrePlaces,
                    montantTotal: $reservation->montantTotal,
                    statut: $reservation->statut,
                    dateExpiration: $reservation->dateExpiration,
                    dateReservation: new DateTime,
                    commentaires: $reservation->commentaires,
                    userName: 'User Name',
                    userEmail: 'user@example.com',
                );
            }

            // Retourner les données sous forme de tableau simple
            $result = [
                'items'   => $enrichedItems,
                'total'   => $paginatedReservations->total ?? 0,
                'page'    => $paginatedReservations->page ?? 1,
                'perPage' => $paginatedReservations->perPage ?? 10,
            ];

            return Result::success($result);

        } catch (Exception $e) {
            return Result::error(
                'SYSTEM_ERROR',
                'Erreur lors de la récupération des réservations : ' . $e->getMessage()
            );
        }
    }
}
