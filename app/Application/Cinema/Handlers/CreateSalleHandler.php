<?php

declare(strict_types=1);

namespace App\Application\Cinema\Handlers;

use Exception;
use Ramsey\Uuid\Uuid;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\Entities\Salle as SalleEntity;
use App\Application\Cinema\Commands\CreateSalleCommand;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final readonly class CreateSalleHandler
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function handle(CreateSalleCommand $command): Result
    {
        try {
            // Verify cinema exists
            $cinema = $this->cinemaRepository->findById(new CinemaId($command->cinemaId));
            if ($cinema === null) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Cinéma non trouvé'
                );
            }

            // Generate default plan salle
            $planSalle = $this->generateDefaultPlanSalle(
                $command->nombreRangees,
                $command->placesParRangee
            );

            // Create salle entity
            $salle = new SalleEntity(
                id: new SalleId(Uuid::uuid4()->toString()),
                cinemaId: new CinemaId($command->cinemaId),
                nom: $command->nom,
                capaciteTotale: $command->capaciteTotale,
                nombreRangees: $command->nombreRangees,
                placesParRangee: $command->placesParRangee,
                placesStandard: $command->placesStandard,
                placesPmr: $command->placesPmr,
                qualiteProjection: array_map(fn ($q) => QualiteProjection::from($q), $command->qualitesVideo),
                qualiteSonore: array_map(fn ($q) => QualiteSonore::from($q), $command->qualitesAudio),
                accessibilitePmr: $command->accessibilitePmr,
                climatisation: $command->climatisation,
                planSalle: $planSalle,
                statut: StatutSalle::from($command->statut),
            );

            // Save to repository
            $savedSalle = $this->salleRepository->save($salle);

            return Result::success($savedSalle);

        } catch (Exception $e) {
            return Result::error(
                'CREATE_SALLE_FAILED',
                'Erreur lors de la création de la salle: ' . $e->getMessage()
            );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function generateDefaultPlanSalle(int $nombreRangees, int $placesParRangee): array
    {
        $plan = [];

        for ($rangee = 1; $rangee <= $nombreRangees; $rangee++) {
            $plan["rangee_{$rangee}"] = [
                'numero'        => $rangee,
                'nombre_sieges' => $placesParRangee,
                'type_sieges'   => $rangee <= 2 ? 'premium' : 'standard',
                'pmr'           => $rangee <= 2, // Les 2 premières rangées sont PMR
            ];
        }

        return $plan;
    }
}
