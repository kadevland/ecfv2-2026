<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use DateTimeImmutable;
use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Application\Reservations\DTOs\ReservationDetailDto;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\User\Repositories\UserProfilRepositoryInterface;
use App\Application\Reservations\Queries\GetReservationDetailQuery;

use App\Domain\Cinema\Entities\Salle;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;
use App\Infrastructure\Database\Models\Cinema\Reservation as ReservationModel;

/**
 * Handler pour récupérer le détail d'une réservation
 */
final class GetReservationDetailHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly UserProfilRepositoryInterface $userProfilRepository,
        private readonly SeanceRepositoryInterface $seanceRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetReservationDetailQuery) {
            return Result::error('INVALID_QUERY', 'Query type not supported');
        }

        return $this->handleGetReservationDetail($query);
    }

    private function handleGetReservationDetail(GetReservationDetailQuery $query): Result
    {
        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Query invalide'
                );
            }

            $reservationId = new ReservationId($query->reservationUuid);
            $reservation   = $this->reservationRepository->findById($reservationId);

            if (!$reservation) {
                return Result::error(
                    'RESERVATION_NOT_FOUND',
                    'Réservation introuvable'
                );
            }



            // Enrichir avec les données utilisateur et séance
            $reservationId = $reservation->id->value;
            $userId        = $reservation->userId->value;
            $seanceId      = $reservation->seanceId->value;

            $userProfil       = $this->userProfilRepository->findByIds([$userId])[$userId] ?? null;
            $seance           = $this->seanceRepository->findByIdsWithRelations([$seanceId])[$seanceId] ?? null;
            $film= FilmModel::find($seance->film_db_id) ?? null;
            $salle= SalleModel::with('cinema')->find($seance->salle_db_id) ?? null;
            $cinema= $salle?->cinema ?? null;
            $reservationModel = ReservationModel::where('uuid',$reservationId)->first();


            //$reservationModel = $this->reservationRepository->findByIds([$reservationId])[$reservationId] ?? null;


            // Extraire les données de la séance et ses relations
            // Données par défaut (relations à implémenter)

            //dd($reservation ,$reservationModel);

            $filmTitre  = $film ? $film->titre : 'Titre inconnu';
            $filmAffcheUrl = $film ? $film->affiche_url : null;
            $seanceDate = $seance?->date_seance ? new DateTimeImmutable($seance->date_seance) : null;
            $salleName  = $salle ? $salle->nom : 'Salle inconnue';
            $cinemaName = $cinema ? $cinema->nom : 'Cinéma inconnu';

            // Extraire les données utilisateur
            // Données par défaut (relations à implémenter)
            $userEmail  = $userProfil?->email ?? 'Email non disponible';
            $userNom    = $userProfil?->nom ?? 'Nom non disponible';
            $userPrenom = $userProfil?->prenom ??'Prénom non disponible';

            $dto = new ReservationDetailDto(
                id: $reservation->id->value,
                numeroReservation: $reservation->numeroReservation,
                userId: $userId,
                userEmail: $userEmail,
                userNom: $userNom,
                userPrenom: $userPrenom,
                seanceId: $seanceId,
                filmTitre: $filmTitre,
                filmAffcheUrl: $filmAffcheUrl,
                seanceDate: $seanceDate,
                cinemaName: $cinemaName,
                salleName: $salleName,
                nombrePlaces: $reservation->nombrePlaces,
                placesDetails: $reservation->placesDetails,
                montantTotal: $reservation->montantTotal,
                montantHt: $reservation->montantHt,
                statut: $reservation->statut,
                dateExpiration: $reservation->dateExpiration,
                commentaires: $reservation->commentaires,
                qrCode: $reservation->qrCode,
                dateCreation: $reservationModel->created_at ,
                dateModification: $reservationModel->updated_at
            );

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                error: 'SYSTEM_ERROR',
                message: 'Erreur lors de la récupération de la réservation : ' . $e->getMessage()
            );
        }
    }
}
