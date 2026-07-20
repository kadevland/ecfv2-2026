<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\CreateCinema;

use Log;
use Exception;
use App\Application\Contracts\Result;
use App\Domain\Shared\Enums\CodePays;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Cinema\Events\CinemaCreated;
use App\Domain\Shared\ValueObjects\Address;
use App\Application\Contracts\CommandInterface;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class CreateCinemaCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof CreateCinemaCommand) {
            return Result::error(
                'INVALID_COMMAND',
                'Le type de commande est invalide'
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
            // Créer l'adresse Value Object
            $adresse = new Address(
                rue: $command->rue,
                ville: $command->ville,
                codePostal: $command->codePostal,
                pays: $command->pays,
            );

            // Créer les Value Objects optionnels
            $telephone = $command->telephone ? PhoneNumber::tryFromInternationalFormat($command->telephone) : null;
            $email     = $command->email ? Email::tryFromString($command->email) : null;

            // Créer coordonnées GPS si fournies
            $coordonneesGps = null;
            if ($command->latitude !== null && $command->longitude !== null) {
                $coordonneesGps = new CoordonneesGps(
                    latitude: $command->latitude,
                    longitude: $command->longitude
                );
            }

            // Créer l'entité Cinema via la méthode factory
            $cinema = Cinema::creer(
                nom: $command->nom,
                adresse: $adresse,
                pays: CodePays::from($command->pays),
                telephone: $telephone,
                email: $email,
                description: $command->description,
                coordonneesGps: $coordonneesGps,
                horairesOuverture: $command->horaires
            );

            // Activer/désactiver si nécessaire
            if (!$command->estActif) {
                $cinema->desactiver();
            }

            // Sauvegarder via le repository
            $saved = $this->cinemaRepository->save($cinema);

            if (!$saved) {
                return Result::error(
                    'SAVE_FAILED',
                    'Erreur lors de la sauvegarde'
                );
            }

            // Déclencher l'événement de création pour synchroniser MongoDB
            event(CinemaCreated::fromCinema($cinema));

            return Result::success($cinema);

        } catch (Exception $e) {
            // Debug: afficher plus d'infos sur l'erreur
            Log::error('Cinema creation failed', [
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'command_data' => [
                    'nom'  => $command->nom,
                    'pays' => $command->pays,
                ],
            ]);

            return Result::error(
                'CREATION_FAILED',
                'Erreur lors de la création: ' . $e->getMessage()
            );
        }
    }
}
