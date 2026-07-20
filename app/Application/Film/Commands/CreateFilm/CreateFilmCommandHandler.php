<?php

declare(strict_types=1);

namespace App\Application\Film\Commands\CreateFilm;

use DateTime;
use Exception;
use App\Domain\Cinema\Entities\Film;
use App\Application\Contracts\Result;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;

final class CreateFilmCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly FilmRepositoryInterface $filmRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof CreateFilmCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Handler expects CreateFilmCommand'
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
            // Convertir la date de sortie
            $dateSortie = DateTime::createFromFormat('Y-m-d', $command->dateSortie);
            if (!$dateSortie) {
                return Result::error(
                    'INVALID_DATE',
                    'Format de date invalide'
                );
            }

            // Convertir la date de fin d'exploitation si fournie
            $dateFinExploitation = null;
            if ($command->dateFinExploitation) {
                $dateFinExploitation = DateTime::createFromFormat('Y-m-d', $command->dateFinExploitation);
                if (!$dateFinExploitation) {
                    return Result::error(
                        'INVALID_END_DATE',
                        'Format de date de fin invalide'
                    );
                }
            }

            // Créer l'entité Film via la méthode factory
            $film = Film::create(
                titre: $command->titre,
                realisateurs: $command->realisateurs,
                genres: $command->genres,
                dureeMinutes: $command->dureeMinutes,
                classification: $command->classification,
                dateSortie: $dateSortie,
                titreFr: $command->titreFr,
                acteursPrincipaux: $command->acteursPrincipaux,
                langueOriginale: $command->langueOriginale,
                sousTitres: $command->sousTitres,
                resume: $command->resume,
                dateFinExploitation: $dateFinExploitation,
                notePresse: $command->notePresse,
                notePublic: $command->notePublic,
                afficheUrl: $command->afficheUrl,
                bandeAnnonceUrl: $command->bandeAnnonceUrl,
                estActif: $command->estActif,
            );

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
                'CREATION_FAILED',
                'Erreur lors de la création du film: ' . $e->getMessage()
            );
        }
    }
}
