<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use App\Application\Shared\Handlers\CommandHandler;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Reservations\Events\ReservationConfirmed;
use App\Application\Shared\Events\EventDispatcherInterface;
use App\Application\Shared\Exceptions\EntityNotFoundException;
use App\Application\Reservations\Commands\ConfirmReservationCommand;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

final readonly class ConfirmReservationHandler implements CommandHandler
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function handle(ConfirmReservationCommand $command): void
    {
        $reservationId = ReservationId::fromString($command->reservationId);
        $reservation   = $this->reservationRepository->findById($reservationId);

        if ($reservation === null) {
            throw EntityNotFoundException::forId('Reservation', $command->reservationId);
        }

        // Confirmer la réservation
        $reservation->confirmer();

        // Générer QR Code si fourni

        $this->reservationRepository->save($reservation);

        // Dispatch event pour synchronisation MongoDB + notifications
        $this->eventDispatcher->dispatch(ReservationConfirmed::fromReservation($reservation));
    }
}
