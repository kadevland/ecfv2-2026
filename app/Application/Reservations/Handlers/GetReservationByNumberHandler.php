<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Reservations\DTOs\ReservationDetailDto;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\User\Repositories\UserProfilRepositoryInterface;
use App\Application\Reservations\Queries\GetReservationByNumberQuery;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;
// Modèles Eloquent pour récupération directe (démo)
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;

final class GetReservationByNumberHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly UserProfilRepositoryInterface $userProfilRepository,
        private readonly SeanceRepositoryInterface $seanceRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetReservationByNumberQuery) {
            return Result::error('INVALID_QUERY', 'Query type not supported');
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Query invalide'
                );
            }

            $reservation = $this->reservationRepository->findByNumero($query->numeroReservation);

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
            $reservationModel = $this->reservationRepository->findByIds([$reservationId])[$reservationId] ?? null;

            // Extraire les données de la séance et ses relations
            // Si les relations Domain ne sont pas chargées, utiliser Eloquent en backup
            if (!$seance?->film && $seance) {
                $film = FilmModel::find($seance->film_db_id);
                $salle = SalleModel::with('cinema')->find($seance->salle_db_id);
                $cinema = $salle?->cinema;

                $filmTitre  = $film ? $film->titre : 'Film inconnu';
                $salleName  = $salle ? $salle->nom : 'Salle inconnue';
                $cinemaName = $cinema ? $cinema->nom : 'Cinéma inconnu';
                $seanceDate = $seance->date_heure_debut ?? new DateTime;
            } else {
                $filmTitre = $seance?->film->titre ?? 'Film inconnu';
                $seanceDate = $seance->date_heure_debut ?? new DateTime;
                $salleName = $seance?->salle->nom ?? 'Salle inconnue';
                $cinemaName = $seance?->salle->cinema->nom ?? 'Cinéma inconnu';
            }

            // Extraire les données utilisateur
            // @phpstan-ignore property.notFound
            $userEmail  = $userProfil?->user->email ?? 'Email non disponible';
            $userNom    = $userProfil?->nom->value ?? 'Nom non disponible';
            $userPrenom = $userProfil?->prenom->value ?? 'Prénom non disponible';

            $dto = new ReservationDetailDto(
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
                dateExpiration: $reservation->dateExpiration,
                commentaires: $reservation->commentaires,
                qrCode: $reservation->qrCode,
                // @phpstan-ignore property.notFound
                dateCreation: $reservationModel->created_at ?? new DateTime,
                // @phpstan-ignore property.notFound
                dateModification: $reservationModel->updated_at ?? new DateTime,
            );

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'SYSTEM_ERROR',
                'Erreur lors de la récupération de la réservation : ' . $e->getMessage()
            );
        }
    }
}
