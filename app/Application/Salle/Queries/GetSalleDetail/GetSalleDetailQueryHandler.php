<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSalleDetail;

use Exception;
use RuntimeException;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Application\Contracts\QueryInterface;
use App\Application\Salle\DTOs\SalleDetailDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class GetSalleDetailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
        private readonly SeanceRepositoryInterface $seanceRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetSalleDetailQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'UUID de la salle requis'
                );
            }

            $salleId = SalleId::fromString($query->salleUuid);

            $salle = $this->salleRepository->findById($salleId);

            if (!$salle) {
                return Result::error(
                    'SALLE_NOT_FOUND',
                    'Salle non trouvée'
                );
            }

            $dto = $this->mapToDetailDto($salle, $query);

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }

    private function mapToDetailDto(mixed $salle, GetSalleDetailQuery $query): SalleDetailDto
    {
        // Charger les séances si demandées
        $seancesAVenir = [];
        if ($query->includeSeances) {
            $seancesEntites = $this->seanceRepository->findBySalleId($salle->id);
            // Filtrer les séances à venir et limiter à 10
            $seancesEntites = array_filter($seancesEntites, fn ($seance) => $seance->isUpcoming());
            $seancesEntites = array_slice($seancesEntites, 0, 10);

            $seancesAVenir = array_map(fn ($seance) => [
                'uuid'      => $seance->id->value,
                'dateHeure' => $seance->dateHeureDebut->format('Y-m-d H:i:s'),
                // @phpstan-ignore property.notFound
                'filmTitre' => $seance->film->titre ?? 'N/A',
                'version'   => $seance->version,
                'prixMin'   => ($seance->getTarification()->getPrixMinimum()?->getAmount() ?? 0) / 100,
            ], $seancesEntites);
        }

        // Charger l'historique des maintenances si demandé
        $historiqueMaintenances = [];
        if ($query->includeMaintenances) {

            $historiqueMaintenances = [];
        }

        // 2. Récupérer le cinéma associé via son repository
        $cinema = $this->cinemaRepository->findById($salle->cinemaId);

        if (!$cinema) {
            throw new RuntimeException('Cinéma associé non trouvé');
        }

        // 3. Construire le DTO avec les données des 2 entités
        return new SalleDetailDto(
            uuid: $salle->id->value,
            nom: $salle->nom,
            capaciteTotale: $salle->capaciteTotale,
            nombreRangees: $salle->nombreRangees,
            placesParRangee: $salle->placesParRangee,
            placesStandard: $salle->placesStandard,
            placesPmr: $salle->placesPmr,
            qualiteProjection: array_map(fn ($q) => $q->value, $salle->qualiteProjection),
            qualiteSonore: array_map(fn ($q) => $q->value, $salle->qualiteSonore),
            climatisation: $salle->climatisation,
            accessibilitePmr: $salle->accessibilitePmr,
            planSalle: $salle->planSalle,
            statut: $salle->statut->value,
            cinemaUuid: $salle->cinemaId->value,
            cinemaDbId: 0,
            cinemaNom: $cinema->nom,
            cinemaVille: $cinema->adresse->ville,
        );
    }
}
