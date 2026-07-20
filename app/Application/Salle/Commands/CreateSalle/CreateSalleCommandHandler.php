<?php

declare(strict_types=1);

namespace App\Application\Salle\Commands\CreateSalle;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Salle;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class CreateSalleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof CreateSalleCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Handler expects CreateSalleCommand'
            );
        }

        // Validation de la command
        $validationErrors = $command->validate();
        if (!empty($validationErrors)) {
            return Result::error(
                $validationErrors,
                'Données invalides: ' . implode(', ', $validationErrors)
            );
        }

        try {
            // Vérifier que le cinéma existe
            $cinemaId = CinemaId::fromString($command->cinemaUuid);
            $cinema   = $this->cinemaRepository->findById($cinemaId);
            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Le cinéma spécifié n\'existe pas'
                );
            }

            // Créer l'entité Salle via la méthode factory
            $salle = Salle::create(
                cinemaId: $cinemaId,
                nom: $command->nom,
                capaciteTotale: $command->capaciteTotale,
                nombreRangees: $command->nombreRangees,
                placesParRangee: $command->placesParRangee,
                placesStandard: $command->placesStandard,
                placesPmr: $command->placesPmr,
                qualiteProjection: $command->qualiteProjection,
                qualiteSonore: $command->qualiteSonore,
                accessibilitePmr: $command->accessibilitePmr,
                climatisation: $command->climatisation,
                planSalle: $command->planSalle,
                statut: $command->statut,
            );

            // Sauvegarder via le repository
            $saved = $this->salleRepository->save($salle);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde de la salle'
                );
            }

            return Result::success($salle);

        } catch (Exception $e) {
            return Result::error(
                'CREATION_FAILED',
                'Erreur lors de la création de la salle: ' . $e->getMessage()
            );
        }
    }
}
