<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use Exception;
use Money\Money;
use Illuminate\Support\Facades\DB;
use App\Application\Contracts\Result;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Application\Contracts\CommandInterface;
use App\Domain\Reservations\Entities\Reservation;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Reservations\Events\ReservationCreated;
use App\Application\Shared\Events\EventDispatcherInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\Reservations\Services\ReservationValidationService;
use App\Application\Reservations\Commands\CreateReservationCommand;
use App\Domain\Reservations\Exceptions\ReservationValidationException;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

final readonly class CreateReservationHandler implements CommandHandlerInterface
{
    private const TARIF_BASE = 1250; // 12.50€ en centimes

    private const TAUX_TVA = 550;   // 5.5% en basis points

    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private SeanceRepositoryInterface $seanceRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ReservationValidationService $validationService,
    ) {}

    public function handle(CommandInterface $command): Result
    {
        try {
            // Type check
            if (!$command instanceof CreateReservationCommand) {
                return Result::error('InvalidCommand', 'Command invalide');
            }

            // 1. Récupérer et valider la séance
            $seance = $this->seanceRepository->findById(SeanceId::fromString($command->seanceId));
            if (!$seance) {
                return Result::error('SeanceNotFound', 'Séance non trouvée');
            }

            // 2. Gestion des places par tarif
            $nombrePlaces  = 0;
            $placesDetails = [];
            $montantTotal  = 0;

            if ($command->placesByTarif !== null) {
                // Calcul avec multi-tarifs
                $tarification = $seance->getTarification();

                foreach ($command->placesByTarif as $typeTarif => $nombre) {
                    $nombre = (int) $nombre;
                    if ($nombre > 0) {
                        $nombrePlaces += $nombre;

                        // Récupérer le prix pour ce tarif
                        $prix = match ($typeTarif) {
                            'normal' => $tarification->getPrixNormal(),
                            'reduit' => $tarification->getPrixReduit(),
                            'enfant' => $tarification->getPrixEnfant(),
                            default  => null
                        };

                        if ($prix === null) {
                            return Result::error(
                                'InvalidTariff',
                                "Tarif '$typeTarif' non disponible pour cette séance"
                            );
                        }

                        // Ajouter au montant total (Money retourne en centimes)
                        $montantTotal += $prix->getAmount() * $nombre;

                        // Stocker les détails
                        $placesDetails[$typeTarif] = $nombre;
                    }
                }
            } elseif ($command->nombrePlaces !== null) {
                $nombrePlaces = $command->nombrePlaces;
                // Prix normal par défaut
                $montantTotal = $nombrePlaces * self::TARIF_BASE;
            } elseif (!empty($command->seats)) {
                // Si on reçoit des sièges, on compte simplement leur nombre
                $nombrePlaces = count($command->seats);
                $montantTotal = $nombrePlaces * self::TARIF_BASE;
            } else {
                return Result::error(
                    'NumberRequired',
                    'Veuillez spécifier le nombre de places pour cette séance'
                );
            }

            // 3. Validation métier complète via le service dédié
            try {
                $this->validationService->validateReservationCreation(
                    seanceId: SeanceId::fromString($command->seanceId),
                    nombrePlaces: $nombrePlaces,
                    dateReservation: new DateTime
                );
            } catch (ReservationValidationException $e) {
                return Result::error('ValidationError', $e->getMessage());
            }

            // 4. Calcul du montant HT (si montantTotal n'a pas déjà été calculé pour multi-tarifs)
            if ($montantTotal === 0) {
                $montantTotal = $nombrePlaces * self::TARIF_BASE;
            }
            $montantHt = (int) round($montantTotal / (1 + self::TAUX_TVA / 10000));

            // 5. Générer un numéro de réservation unique
            $numeroReservation = $this->reservationRepository->generateReservationNumber();

            // 6. Transaction atomique pour intégrité critique
            $reservation = DB::transaction(function () use (
                $numeroReservation, $command, $nombrePlaces, $placesDetails,
                $montantTotal, $montantHt
            ) {
                // Créer la réservation
                $reservation = Reservation::creer(
                    numeroReservation: $numeroReservation,
                    userId: UserId::fromString($command->userId),
                    seanceId: SeanceId::fromString($command->seanceId),
                    nombrePlaces: $nombrePlaces,
                    placesDetails: $placesDetails,
                    montantTotal: Money::EUR($montantTotal),
                    montantHt: Money::EUR($montantHt),
                    tauxTva: TauxTva::fromBasisPoints(self::TAUX_TVA),
                    commentaires: $command->commentaires,
                    dateExpiration: $command->dateExpiration ? new DateTime($command->dateExpiration) : null,
                );

                // Sauvegarder de manière atomique
                $this->reservationRepository->save($reservation);

                // Synchronisation SYNCHRONE pour éviter race conditions
                $this->eventDispatcher->dispatch(ReservationCreated::fromReservation($reservation));

                return $reservation;
            });

            return Result::success($reservation->id->value);
        } catch (Exception $e) {
            return Result::error('UnexpectedError', $e->getMessage());
        }
    }
}
