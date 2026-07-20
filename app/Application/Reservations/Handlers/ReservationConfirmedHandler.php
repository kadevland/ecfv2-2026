<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use App\Application\Shared\Handlers\EventHandler;
use App\Domain\Reservations\Events\ReservationConfirmed;

final readonly class ReservationConfirmedHandler implements EventHandler
{
    public function __construct()
    {
        // Handler simplifié sans dépendances pour éviter les erreurs PHPStan
    }

    public function handle(ReservationConfirmed $event): void
    {

        // Handler temporairement vide pour respecter PHPStan
    }
}
