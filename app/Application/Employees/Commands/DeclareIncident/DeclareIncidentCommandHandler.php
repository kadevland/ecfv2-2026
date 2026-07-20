<?php

declare(strict_types=1);

namespace App\Application\Employees\Commands\DeclareIncident;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Employees\Entities\Incident;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\CommandInterface;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Users\Repositories\EmployeRepositoryInterface;
use App\Domain\Employees\Repositories\IncidentRepositoryInterface;

final class DeclareIncidentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly IncidentRepositoryInterface $incidentRepository,
        private readonly EmployeRepositoryInterface $employeRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function handle(CommandInterface $command): Result
    {
        assert($command instanceof DeclareIncidentCommand);

        // Validation de la command
        $validationErrors = $command->validate();
        if (!empty($validationErrors)) {
            return Result::error(
                $validationErrors,
                'Données invalides: ' . implode(', ', $validationErrors)
            );
        }

        try {
            // Vérifier que l'employé existe
            $emploiId = EmploiId::fromString($command->emploiDeclarantUuid);
            $employe  = $this->employeRepository->findById($emploiId);
            if (!$employe) {
                return Result::error(
                    'EMPLOYE_NOT_FOUND',
                    'L\'employé déclarant n\'existe pas'
                );
            }

            // Vérifier que le cinéma existe
            $cinemaId = CinemaId::fromString($command->cinemaUuid);
            $cinema   = $this->cinemaRepository->findById($cinemaId);
            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Le cinéma spécifié n\'existe pas'
                );
            }

            // Vérifier que l'employé appartient bien au cinéma
            if (!$employe->travailleDansCinema($cinemaId)) {
                return Result::error(
                    'EMPLOYE_NOT_IN_CINEMA',
                    'L\'employé ne travaille pas dans ce cinéma'
                );
            }

            // Vérifier la salle si spécifiée
            $salleId = null;
            if ($command->salleUuid !== null) {
                $salleId = SalleId::fromString($command->salleUuid);
                $salle   = $this->salleRepository->findById($salleId);

                if (!$salle) {
                    return Result::error(
                        'SALLE_NOT_FOUND',
                        'La salle spécifiée n\'existe pas'
                    );
                }

                // Vérifier que la salle appartient au cinéma
                // Note: Assuming Salle entity has a cinemaId property
                if (!$salle->cinemaId->equals($cinemaId)) {
                    return Result::error(
                        'SALLE_NOT_IN_CINEMA',
                        'La salle n\'appartient pas au cinéma spécifié'
                    );
                }
            }

            // Créer l'incident via la méthode factory du domaine
            $incident = Incident::declarer(
                emploiDeclarantId: $emploiId,
                cinemaId: $cinemaId,
                typeIncident: $command->typeIncident->value,
                severite: $command->severite->value,
                titre: $command->titre,
                description: $command->description,
                salleId: $salleId
            );

            // Ajouter les pièces jointes si présentes
            if ($command->piecesJointes !== null && !empty($command->piecesJointes)) {
                foreach ($command->piecesJointes as $piece) {
                    $incident->ajouterPieceJointe(
                        $piece['filename'],
                        $piece['path'],
                        $piece['type'] ?? null
                    );
                }
            }

            // Sauvegarder via le repository
            $saved = $this->incidentRepository->save($incident);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde de l\'incident'
                );
            }

            // Si incident critique, notifier le manager
            if ($incident->isCritical()) {

                // $this->notificationService->notifyManager($incident);
            }

            return Result::success([
                'incident' => $incident,
                'message'  => sprintf(
                    'Incident %s déclaré avec succès. Numéro: %s',
                    $incident->titre,
                    $incident->id->value
                ),
            ]);

        } catch (Exception $e) {
            return Result::error(
                'DECLARATION_FAILED',
                'Erreur lors de la déclaration de l\'incident: ' . $e->getMessage()
            );
        }
    }
}
