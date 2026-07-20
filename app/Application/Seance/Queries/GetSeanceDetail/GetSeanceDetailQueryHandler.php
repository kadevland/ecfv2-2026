<?php

declare(strict_types=1);

namespace App\Application\Seance\Queries\GetSeanceDetail;

use Exception;
use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Entities\Film;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Application\Contracts\QueryInterface;
use App\Application\Seance\DTOs\SeanceDetailDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\Cinema\Repositories\ReservationRepositoryInterface;

final class GetSeanceDetailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SeanceRepositoryInterface $seanceRepository,
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetSeanceDetailQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'UUID de la séance requis'
                );
            }

            $seanceId = SeanceId::fromString($query->seanceUuid);

            $seance = $this->seanceRepository->findById($seanceId);

            if (!$seance) {
                return Result::error(
                    'SEANCE_NOT_FOUND',
                    'Séance non trouvée'
                );
            }

            $dto = $this->mapToDetailDto($seance, $query);

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }

    private function mapToDetailDto(Seance $seance, GetSeanceDetailQuery $query): SeanceDetailDto
    {
        // Charger le film
        $film = $this->filmRepository->findById($seance->filmId);

        // Charger la salle
        $salle = $this->salleRepository->findById($seance->salleId);

        // Charger les réservations si demandées
        $reservations        = [];
        $reservationsEntites = $this->reservationRepository->findBySeanceId($seance->id);

        if ($query->includeReservations) {
            $reservations = array_map(fn ($reservation) => [
                'uuid'            => $reservation->id->value,
                'utilisateurUuid' => $reservation->utilisateurId->value,
                'nombrePlaces'    => $reservation->nombrePlaces,
                'statut'          => $reservation->statut,
                'montantTotal'    => $reservation->montantTotal->toFloat(),
                'dateReservation' => $reservation->dateReservation->format('Y-m-d H:i:s'),
            ], $reservationsEntites);
        }

        // Calculer les places disponibles
        $placesReservees = array_sum(array_map(fn ($reservation) => $reservation->nombrePlaces, $reservationsEntites));
        /** @phpstan-ignore-next-line */
        $placesTotales     = $salle->capaciteTotale ?? 0;
        $placesDisponibles = max(0, $placesTotales - $placesReservees);

        // Charger les places occupées si demandées
        $placesOccupees = [];
        if ($query->includePlacesOccupees) {

            $placesOccupees = [];
        }

        // Préparer la tarification

        $tarification = [
            'prixMin'             => $seance->getTarification()->getPrixMinimum()?->getAmount() / 100,
            'prixMax'             => $seance->getTarification()->getPrixMaximum()?->getAmount() / 100,
            'categories'          => array_map(fn ($type) => $type->value, $seance->getTarification()->getTypesDisponibles()),
            'tarifsBase'          => $seance->getTarification()->tarifsBase,
            'supplementsSpeciaux' => $seance->getTarification()->supplementsSpeciaux ?? [],
            'reductionsSpeciales' => $seance->getTarification()->reductionsSpeciales ?? [],
        ];

        return new SeanceDetailDto(
            uuid: $seance->id->value,
            dateHeure: $seance->dateHeureDebut->format('Y-m-d H:i:s'),
            dateHeureDebut: $seance->dateHeureDebut->format('Y-m-d H:i:s'),
            dateHeureFin: $seance->dateHeureFin->format('Y-m-d H:i:s'),
            filmTitre: $film->titre ?? 'Film N/A',
            filmUuid: $seance->filmId->value,
            filmAfficheUrl: $film->afficheUrl ?? '',
            filmDureeMinutes: $film->dureeMinutes ?? 0,
            filmClassification: $film->classification ?? null,
            filmClassificationLabel: $film->classification ? \App\Domain\Enums\ClassificationFilm::from($film->classification)->label() : null,
            salleNom: $salle->nom ?? 'Salle N/A',
            salleUuid: $seance->salleId->value,
            /** @phpstan-ignore-next-line */
            salleNumero: $salle->numero ?? 0,
            /** @phpstan-ignore-next-line */
            salleCapacite: $salle->capaciteTotale ?? 0,
            version: $seance->version,
            technologies: [], // Plus de technologies, tout est en colonnes directes
            tarification: $tarification,
            dureeAdditionnelle: $seance->dureeAdditionnelle ?? $this->calculateDureeAdditionnelle($seance, $film),
            qualiteProjection: $seance->qualiteProjection?->value,
            qualiteSonore: $seance->qualiteSonore?->value,
            placementLibre: $seance->placementLibre,
            placesDisponibles: $placesDisponibles,
            placesTotales: $placesTotales,
            statut: $seance->statut->value,
            statutLabel: $seance->statut->label(),
            tauxTva: $seance->tauxTva->getPercentage(),
            devise: $seance->devise->code,
            estComplete: $placesDisponibles === 0,
            estAnnulee: $seance->statut === StatutSeance::ANNULEE,
            reservations: $reservations,
            placesOccupees: $placesOccupees,
        );
    }

    private function calculateDureeAdditionnelle(Seance $seance, ?Film $film): ?int
    {
        if (!$film) {
            return null;
        }

        // Calculer la durée totale de la séance en minutes
        $debut              = \Carbon\Carbon::parse($seance->dateHeureDebut->format('Y-m-d H:i:s'));
        $fin                = \Carbon\Carbon::parse($seance->dateHeureFin->format('Y-m-d H:i:s'));
        $dureeSeanceMinutes = $fin->diffInMinutes($debut);

        // Soustraire la durée du film pour obtenir le temps additionnel
        $dureeAdditionnelle = (int) $dureeSeanceMinutes - $film->dureeMinutes;

        return $dureeAdditionnelle > 0 ? $dureeAdditionnelle : null;
    }
}
