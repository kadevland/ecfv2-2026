<?php

declare(strict_types=1);

namespace App\Application\Salle\Commands\UpdateSalle;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;

final class UpdateSalleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        assert($command instanceof UpdateSalleCommand);

        try {
            if (!$command->isValid()) {
                return Result::error(
                    'INVALID_COMMAND',
                    'Données de commande invalides'
                );
            }

            if (!$command->hasUpdates()) {
                return Result::error(
                    'NO_UPDATES',
                    'Aucune modification fournie'
                );
            }

            $salleId = SalleId::fromString($command->salleUuid);

            $salle = $this->salleRepository->findById($salleId);

            if (!$salle) {
                return Result::error(
                    'SALLE_NOT_FOUND',
                    'Salle non trouvée'
                );
            }

            // Mise à jour des propriétés si fournies
            if ($command->nom !== null) {
                $salle->updateNom($command->nom);
            }

            if ($command->capaciteTotale !== null) {
                $salle->updateCapaciteTotale($command->capaciteTotale);
            }

            if ($command->nombreRangees !== null) {
                $salle->updateNombreRangees($command->nombreRangees);
            }

            if ($command->placesParRangee !== null) {
                $salle->updatePlacesParRangee($command->placesParRangee);
            }

            if ($command->placesStandard !== null) {
                $salle->updatePlacesStandard($command->placesStandard);
            }

            if ($command->placesPmr !== null) {
                $salle->updatePlacesPmr($command->placesPmr);
            }

            if ($command->qualiteProjection !== null) {
                $salle->updateQualiteProjection($command->qualiteProjection);
            }

            if ($command->qualiteSonore !== null) {
                $salle->updateQualiteSonore($command->qualiteSonore);
            }

            if ($command->accessibilitePmr !== null) {
                $salle->updateAccessibilitePmr($command->accessibilitePmr);
            }

            if ($command->climatisation !== null) {
                $salle->updateClimatisation($command->climatisation);
            }

            if ($command->planSalle !== null) {
                $salle->updatePlanSalle($command->planSalle);
            }

            if ($command->statut !== null) {
                $salle->updateStatut($command->statut);
            }

            $this->salleRepository->save($salle);

            return Result::success([
                'uuid'    => $salle->id->value,
                'message' => 'Salle mise à jour avec succès',
            ]);

        } catch (Exception $e) {
            return Result::error(
                'UPDATE_FAILED',
                'Erreur lors de la mise à jour: ' . $e->getMessage()
            );
        }
    }
}
