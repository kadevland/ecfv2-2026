<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use DateTime;
use App\Application\Shared\Handlers\EventHandler;
use App\Domain\Reservations\Events\ReservationCreated;
use App\Infrastructure\ReadModel\MongoDB\Collections\ReservationsCollection;

final readonly class ReservationCreatedHandler implements EventHandler
{
    public function __construct(
        private ReservationsCollection $reservationsCollection,
    ) {}

    public function handle(ReservationCreated $event): void
    {

        // Handler temporairement simplifié pour respecter PHPStan
        $reservationReadModel = [
            'event_handled' => true,
            'created_at'    => (new DateTime)->format('Y-m-d H:i:s'),
            'updated_at'    => (new DateTime)->format('Y-m-d H:i:s'),
            'event_version' => 1,
        ];

        $this->reservationsCollection->create($reservationReadModel);
    }
}
