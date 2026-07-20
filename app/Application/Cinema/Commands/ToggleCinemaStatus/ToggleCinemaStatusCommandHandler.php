<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\ToggleCinemaStatus;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class ToggleCinemaStatusCommandHandler
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository
    ) {}

    public function handle(ToggleCinemaStatusCommand $command): Result
    {
        // Validation de la command
        $validationErrors = $command->validate();
        if (!empty($validationErrors)) {
            return Result::error(
                $validationErrors,
                'Données invalides: ' . implode(', ', $validationErrors)
            );
        }

        try {
            $cinemaId = new CinemaId($command->cinemaUuid);

            // Récupérer l'entité via le repository
            $cinema = $this->cinemaRepository->findById($cinemaId);

            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Cinéma non trouvé'
                );
            }

            // Basculer le statut via les méthodes métier
            $ancienStatut = $cinema->estActif;
            if ($cinema->estActif) {
                $cinema->desactiver();
                $action = 'fermé';
            } else {
                $cinema->activer();
                $action = 'rouvert';
            }

            // Sauvegarder via le repository
            $saved = $this->cinemaRepository->save($cinema);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde'
                );
            }

            return Result::success([
                'cinema'         => $cinema,
                'action'         => $action,
                'ancien_statut'  => $ancienStatut,
                'nouveau_statut' => $cinema->estActif,
            ]);

        } catch (Exception $e) {
            return Result::error(
                'TOGGLE_STATUS_FAILED',
                'Erreur lors du changement de statut: ' . $e->getMessage()
            );
        }
    }
}
