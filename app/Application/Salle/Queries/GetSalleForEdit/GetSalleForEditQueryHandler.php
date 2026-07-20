<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSalleForEdit;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Application\Salle\DTOs\SalleEditDto;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class GetSalleForEditQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetSalleForEditQuery) {
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

            // 1. Récupérer la salle
            $salleId = SalleId::fromString($query->salleUuid);
            $salle   = $this->salleRepository->findById($salleId);

            if (!$salle) {
                return Result::error(
                    'SALLE_NOT_FOUND',
                    'Salle non trouvée'
                );
            }

            // 2. Récupérer le cinéma associé
            $cinema = $this->cinemaRepository->findById($salle->cinemaId);

            if (!$cinema) {
                return Result::error('CINEMA_NOT_FOUND', 'Cinéma associé non trouvé');
            }

            // 3. Construire le DTO manuellement depuis les entités
            $dto = new SalleEditDto(
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
                cinemaNom: $cinema->nom,
                cinemaVille: $cinema->adresse->ville,
                estDisponible: $salle->statut->value === 'ACTIVE',
            );

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }
}
