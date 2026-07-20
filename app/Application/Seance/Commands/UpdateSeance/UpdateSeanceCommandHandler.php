<?php

declare(strict_types=1);

namespace App\Application\Seance\Commands\UpdateSeance;

use Exception;
use Carbon\CarbonImmutable;
use App\Domain\Enums\StatutSeance;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class UpdateSeanceCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SeanceRepositoryInterface $seanceRepository,
        private readonly FilmRepositoryInterface $filmRepository,
        /** @phpstan-ignore-next-line */
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof UpdateSeanceCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Type de commande invalide'
            );
        }

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

            $seanceId = SeanceId::fromString($command->seanceUuid);

            $seance = $this->seanceRepository->findById($seanceId);

            if (!$seance) {
                return Result::error(
                    'SEANCE_NOT_FOUND',
                    'Séance non trouvée'
                );
            }

            // Vérifications de cohérence métier
            if ($command->dateHeureDebut !== null) {
                $dateDebut = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $command->dateHeureDebut);

                // Vérifier que la date n'est pas dans le passé
                if ($dateDebut->isPast()) {
                    return Result::error(
                        'INVALID_DATE',
                        'Impossible de programmer une séance dans le passé'
                    );
                }

                // Récupérer le film pour avoir sa durée
                $film = $this->filmRepository->findById($seance->filmId);
                if (!$film) {
                    return Result::error(
                        'FILM_NOT_FOUND',
                        'Film de la séance non trouvé'
                    );
                }

                // Calculer la nouvelle heure de fin avec la durée du film + temps additionnel
                $dureeAdditionnelle = $command->dureeAdditionnelle ?? 30; // Par défaut 30 min
                $dureeTotal         = $film->dureeMinutes + $dureeAdditionnelle;
                $dateFin            = $dateDebut->addMinutes($dureeTotal);

                // Mettre à jour avec les bonnes dates
                $seance->updateDateHeureDebutAvecFin($dateDebut, $dateFin);
            }

            // Pour le film et la salle, comme on a décidé qu'ils ne sont pas modifiables,
            // on ignore simplement ces paramètres s'ils sont fournis
            // (la vue ne les envoie normalement plus)

            // Vérifier les conflits seulement si on change VRAIMENT la date/heure
            $dateChanged = false;
            if ($command->dateHeureDebut !== null) {
                $newDateDebut     = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $command->dateHeureDebut);
                $currentDateDebut = $seance->dateHeureDebut;

                // Vérifier si la date a réellement changé (comparaison au niveau minute)
                $dateChanged = $currentDateDebut->format('Y-m-d H:i') !== $newDateDebut->format('Y-m-d H:i');
            }

            if ($dateChanged) {
                $dateDebut = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $command->dateHeureDebut);

                // Récupérer le film pour calculer la fin
                $film = $this->filmRepository->findById($seance->filmId);
                if ($film) {
                    $dureeAdditionnelle = $command->dureeAdditionnelle ?? $seance->dureeAdditionnelle ?? 30;
                    $dureeTotal         = $film->dureeMinutes + $dureeAdditionnelle;
                    $dateFin            = $dateDebut->addMinutes($dureeTotal);

                    // Vérifier disponibilité de la salle avec la nouvelle date
                    // en excluant directement la séance actuelle dans la requête SQL
                    $conflictSeances = $this->seanceRepository->findConflictingSeances(
                        $seance->salleId,  // Utiliser la salle actuelle
                        $dateDebut,
                        $dateFin,
                        $seanceId  // Exclure la séance actuelle directement dans la requête
                    );

                    if (!empty($conflictSeances)) {
                        return Result::error(
                            'SALLE_CONFLICT',
                            'La salle est déjà occupée sur ce nouveau créneau horaire'
                        );
                    }
                }
            }

            // Autres mises à jour
            if ($command->version !== null) {
                $seance->updateVersion($command->version);
            }

            if ($command->dureeAdditionnelle !== null) {
                $seance->updateDureeAdditionnelle($command->dureeAdditionnelle);
            }

            if ($command->qualiteProjection !== null) {
                $seance->updateQualiteProjection(QualiteProjection::from($command->qualiteProjection));
            }

            if ($command->qualiteSonore !== null) {
                $seance->updateQualiteSonore(QualiteSonore::from($command->qualiteSonore));
            }

            if ($command->tarification !== null) {
                $seance->updateTarification($command->tarification);
            }

            if ($command->placementLibre !== null) {
                $seance->updatePlacementLibre($command->placementLibre);
            }

            if ($command->statut !== null) {
                $seance->updateStatut(StatutSeance::from($command->statut));
            }

            $this->seanceRepository->save($seance);

            return Result::success([
                'uuid'    => $seance->id->value,
                'message' => 'Séance mise à jour avec succès',
            ]);

        } catch (Exception $e) {
            return Result::error(
                'UPDATE_FAILED',
                'Erreur lors de la mise à jour: ' . $e->getMessage()
            );
        }
    }
}
