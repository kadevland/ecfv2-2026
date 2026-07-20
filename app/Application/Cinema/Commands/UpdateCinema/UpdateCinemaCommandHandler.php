<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\UpdateCinema;

use Exception;
use InvalidArgumentException;
use App\Application\Contracts\Result;
use App\Domain\Shared\Enums\CodePays;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\CommandInterface;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

// use App\Domain\Cinema\Events\CinemaUpdated;

final class UpdateCinemaCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {

        if (!$command instanceof UpdateCinemaCommand) {

            throw new InvalidArgumentException('Expected UpdateCinemaCommand');
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
            $cinemaId = new CinemaId($command->cinemaUuid);

            // Récupérer l'entité via le repository
            $cinema = $this->cinemaRepository->findById($cinemaId);

            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Cinéma non trouvé'
                );
            }

            $updatedFields = [];

            // Utiliser les méthodes métier de l'entité
            if ($command->nom !== null) {
                $cinema->changerNom($command->nom);
                $updatedFields[] = 'nom';
            }

            if ($command->description !== null) {
                $cinema->changerDescription($command->description);
                $updatedFields[] = 'description';
            }

            if ($command->estActif !== null) {
                if ($command->estActif) {
                    $cinema->activer();
                } else {
                    $cinema->desactiver();
                }
                $updatedFields[] = 'est_actif';
            }

            // Mettre à jour l'adresse si des champs adresse sont fournis
            if ($command->rue !== null || $command->ville !== null || $command->codePostal !== null || $command->pays !== null) {

                $nouvelleAdresse = new Address(
                    rue: $command->rue ?? $cinema->adresse->rue,
                    ville: $command->ville ?? $cinema->adresse->ville,
                    codePostal: $command->codePostal ?? $cinema->adresse->codePostal,
                    pays: $command->pays ?? $cinema->pays->value
                );

                $nouveauPays = $command->pays ? CodePays::from($command->pays) : $cinema->pays;

                $cinema->changerAdresse($nouvelleAdresse, $nouveauPays);
                $updatedFields[] = 'adresse';
                if ($command->pays !== null) {
                    $updatedFields[] = 'pays';
                }
            }

            // Mettre à jour les coordonnées GPS si fournies
            if ($command->latitude !== null || $command->longitude !== null) {
                $nouvellesCoordonnees = null;

                if ($command->latitude !== null && $command->longitude !== null) {
                    $nouvellesCoordonnees = new CoordonneesGps(
                        latitude: $command->latitude,
                        longitude: $command->longitude
                    );
                }

                $cinema->changerCoordonneesGps($nouvellesCoordonnees);
                $updatedFields[] = 'coordonnees_gps';
            }

            // Mettre à jour les contacts
            if ($command->telephone !== null || $command->email !== null) {
                $nouveauTelephone = $command->telephone ? PhoneNumber::tryFromInternationalFormat($command->telephone) : $cinema->telephone;
                $nouvelEmail      = $command->email ? Email::tryFromString($command->email) : $cinema->email;

                $cinema->changerContact($nouveauTelephone, $nouvelEmail);
                if ($command->telephone !== null) {
                    $updatedFields[] = 'telephone';
                }
                if ($command->email !== null) {
                    $updatedFields[] = 'email';
                }
            }

            // Mettre à jour les horaires
            if ($command->horaires !== null) {
                // Créer le VO de manière robuste jour par jour
                $horairesVO = $this->createHorairesOuvertureSafe($command->horaires);
                if ($horairesVO !== null) {
                    $cinema->changerHorairesOuverture($horairesVO);
                    $updatedFields[] = 'horaires';
                }
            }

            // Sauvegarder via le repository
            $saved = $this->cinemaRepository->save($cinema);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde'
                );
            }

            // Les événements de domaine sont dispatchés automatiquement par le repository

            return Result::success([
                'cinema'        => $cinema,
                'updatedFields' => $updatedFields,
            ]);

        } catch (Exception $e) {
            return Result::error(
                'UPDATE_FAILED',
                'Erreur lors de la mise à jour: ' . $e->getMessage()
            );
        }
    }

    /**
     * Créer HorairesOuverture de manière robuste jour par jour
     * Si un jour échoue -> jour fermé, pas tout planter
     *
     * @param array<string, mixed> $horaireData
     */
    private function createHorairesOuvertureSafe(array $horaireData): ?\App\Domain\Shared\ValueObjects\HorairesOuverture
    {
        try {
            $horairesJours = [];

            foreach (\App\Domain\Shared\Enums\JourSemaine::cases() as $jour) {
                $jourData = $horaireData[$jour->value] ?? null;

                try {
                    // Si pas de données ou pas ouvert -> fermé
                    if (!$jourData || empty($jourData['ouvert'])) {
                        $horairesJours[$jour->value] = \App\Domain\Shared\ValueObjects\HoraireJournalier::ferme();

                        continue;
                    }

                    // Essayer de créer l'horaire pour ce jour
                    $horairesJours[$jour->value] = \App\Domain\Shared\ValueObjects\HoraireJournalier::fromArray($jourData);

                } catch (Exception $e) {
                    // Si ce jour échoue -> jour fermé
                    $horairesJours[$jour->value] = \App\Domain\Shared\ValueObjects\HoraireJournalier::ferme();
                }
            }

            // Créer le VO global avec skipValidation pour éviter le check "au moins un jour ouvert"
            return new \App\Domain\Shared\ValueObjects\HorairesOuverture(
                lundi: $horairesJours['lundi'],
                mardi: $horairesJours['mardi'],
                mercredi: $horairesJours['mercredi'],
                jeudi: $horairesJours['jeudi'],
                vendredi: $horairesJours['vendredi'],
                samedi: $horairesJours['samedi'],
                dimanche: $horairesJours['dimanche'],
                skipValidation: true
            );

        } catch (Exception $e) {
            // Si tout échoue, retourner null
            return null;
        }
    }
}
