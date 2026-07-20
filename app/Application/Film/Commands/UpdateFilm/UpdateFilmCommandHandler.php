<?php

declare(strict_types=1);

namespace App\Application\Film\Commands\UpdateFilm;

use Exception;
use Carbon\CarbonImmutable;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;

final class UpdateFilmCommandHandler implements CommandHandlerInterface
{
    public function __construct (
        private readonly FilmRepositoryInterface $filmRepository
    ) {}

    public function handle (CommandInterface $command) : Result
    {
        if (!$command instanceof UpdateFilmCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Handler expects UpdateFilmCommand'
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

        // Vérifier qu'il y a au moins une mise à jour
        if (!$command->hasUpdates()) {
            return Result::error(
                'NO_UPDATES',
                'Aucune donnée à mettre à jour'
            );
        }

        try {


            $filmId = FilmId::fromString($command->filmUuid);

            // Récupérer l'entité via le repository
            $film = $this->filmRepository->findById($filmId);

            if (!$film) {
                return Result::error(
                    'FILM_NOT_FOUND',
                    'Film non trouvé'
                );
            }

            // Utiliser les méthodes métier de l'entité Film
            if ($command->titre !== null || $command->titreOriginal !== null) {
                $film->changerTitre(
                    $command->titre ?? $film->titre,
                    $command->titreOriginal ?? $film->titreOriginal
                );
            }

            if ($command->realisateurs !== null) {
                $film->changerRealisateurs($command->realisateurs);
            }

            if ($command->acteursPrincipaux !== null) {
                $film->changerActeursPrincipaux($command->acteursPrincipaux);
            }

            if ($command->genre !== null) {
                // La DB stocke genre comme string, pas array
                $film->changerGenres(is_array($command->genre) ? $command->genre : [$command->genre]);
            }

            if ($command->dureeMinutes !== null) {
                $film->changerDuree($command->dureeMinutes);
            }

            if ($command->classification !== null) {
                $film->changerClassification($command->classification);
            }

            if ($command->dateSortie !== null) {
                $dateSortie = CarbonImmutable::createFromFormat('Y-m-d', $command->dateSortie);
                if (!$dateSortie) {
                    return Result::error(
                        'INVALID_RELEASE_DATE',
                        'Format de date de sortie invalide'
                    );
                }
                $film->changerDateSortie($dateSortie);
            }

            if ($command->synopsis !== null) {
                $film->changerSynopsis($command->synopsis);
            }

            if ($command->afficheUrl !== null) {
                $film->changerAffiche($command->afficheUrl);
            }

            if ($command->bandeAnnonceUrl !== null) {
                $film->changerBandeAnnonce($command->bandeAnnonceUrl);
            }

            // Les nouveaux champs ajoutés
            if ($command->acteursPrincipaux !== null) {

            }

            if ($command->paysOrigine !== null) {

            }

            if ($command->langueOriginale !== null) {

            }

            if ($command->producteur !== null) {

            }

            if ($command->noteCritique !== null) {

            }

            if ($command->notePublic !== null) {

            }

            if ($command->statut !== null) {

            }

            if ($command->estActif !== null) {
                if ($command->estActif) {
                    $film->activer();
                } else {
                    $film->desactiver();
                }
            }

            // Sauvegarder via le repository
            $saved = $this->filmRepository->save($film);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde du film'
                );
            }

            return Result::success($film);

        } catch (Exception $e) {
            return Result::error(
                'UPDATE_FAILED',
                'Erreur lors de la mise à jour du film: ' . $e->getMessage()
            );
        }
    }
}
