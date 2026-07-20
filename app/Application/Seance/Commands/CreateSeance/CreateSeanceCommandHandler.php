<?php

declare(strict_types=1);

namespace App\Application\Seance\Commands\CreateSeance;

use DateTime;
use Exception;
use App\Domain\Enums\StatutSeance;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Application\Contracts\CommandInterface;
use App\Domain\Cinema\ValueObjects\Tarification;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class CreateSeanceCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SeanceRepositoryInterface $seanceRepository,
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof CreateSeanceCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Handler expects CreateSeanceCommand'
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
            // Vérifier que le film existe
            $filmId = FilmId::fromString($command->filmUuid);
            $film   = $this->filmRepository->findById($filmId);
            if (!$film) {
                return Result::error(
                    'FILM_NOT_FOUND',
                    'Le film spécifié n\'existe pas'
                );
            }

            // Vérifier que la salle existe
            $salleId = SalleId::fromString($command->salleUuid);
            $salle   = $this->salleRepository->findById($salleId);
            if (!$salle) {
                return Result::error(
                    'SALLE_NOT_FOUND',
                    'La salle spécifiée n\'existe pas'
                );
            }

            // Vérifier que la salle est disponible
            if (!$salle->isAvailable()) {
                return Result::error(
                    'SALLE_NOT_AVAILABLE',
                    'La salle n\'est pas disponible'
                );
            }

            // Convertir la date de début
            $dateHeureDebut = DateTime::createFromFormat('Y-m-d H:i:s', $command->dateHeureDebut);

            if (!$dateHeureDebut) {
                return Result::error(
                    'INVALID_DATE_DEBUT',
                    'Format de date de début invalide'
                );
            }

            // Récupérer le film pour calculer la durée
            $film = $this->filmRepository->findById($filmId);
            if (!$film) {
                return Result::error(
                    'FILM_NOT_FOUND',
                    'Film non trouvé'
                );
            }

            // Calculer dateHeureFin = dateHeureDebut + durée film + durée additionnelle
            $dureeTotal   = $film->dureeMinutes + $command->dureeAdditionnelle;
            $dateHeureFin = (clone $dateHeureDebut)->modify("+{$dureeTotal} minutes");

            // Vérifier les conflits de séances
            $conflictingSeances = $this->seanceRepository->findConflictingSeances(
                $salleId,
                $dateHeureDebut,
                $dateHeureFin
            );

            if (!empty($conflictingSeances)) {
                return Result::error(
                    'SEANCE_CONFLICT',
                    'Une autre séance est programmée sur ce créneau dans cette salle'
                );
            }

            // Créer la tarification
            $tarification = $this->createTarification($command);

            // Créer le taux TVA
            $tauxTva = TauxTva::fromPercentage($command->tauxTva);

            // Créer la devise
            $devise = Devise::fromString($command->devise);

            // Créer l'entité Seance via la méthode factory
            $seance = Seance::programmer(
                filmId: $filmId,
                salleId: $salleId,
                dateHeureDebut: $dateHeureDebut,
                dateHeureFin: $dateHeureFin,
                version: $command->version,
                tarification: $tarification,
                tauxTva: $tauxTva,
                devise: $devise,
                placementLibre: $command->placementLibre
            );

            // Changer le statut si nécessaire
            if ($command->statut !== StatutSeance::PROGRAMMEE->value) {
                $seance->changerStatut(StatutSeance::from($command->statut));
            }

            // Sauvegarder via le repository
            $saved = $this->seanceRepository->save($seance);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde de la séance'
                );
            }

            return Result::success($seance);

        } catch (Exception $e) {
            return Result::error(
                'CREATION_FAILED',
                'Erreur lors de la création de la séance: ' . $e->getMessage()
            );
        }
    }

    private function createTarification(CreateSeanceCommand $command): Tarification
    {
        // Convertir les prix de euros vers centimes et les formater correctement
        $tarifsBase = [];
        foreach ($command->tarifsBase as $type => $prix) {
            $tarifsBase[$type] = (int) round((float) $prix * 100); // Conversion euros -> centimes
        }

        // Convertir les suppléments si présents
        $supplementsSpeciaux = null;
        if ($command->supplementsSpeciaux) {
            $supplementsSpeciaux = [];
            foreach ($command->supplementsSpeciaux as $type => $prix) {
                $supplementsSpeciaux[$type] = (int) round((float) $prix * 100);
            }
        }

        // Convertir les réductions si présentes
        $reductionsSpeciales = null;
        if ($command->reductionsSpeciales) {
            $reductionsSpeciales = [];
            foreach ($command->reductionsSpeciales as $type => $prix) {
                $reductionsSpeciales[$type] = (int) round((float) $prix * 100);
            }
        }

        return Tarification::create(
            tarifsBase: $tarifsBase,
            supplementsSpeciaux: $supplementsSpeciaux,
            reductionsSpeciales: $reductionsSpeciales
        );
    }
}
