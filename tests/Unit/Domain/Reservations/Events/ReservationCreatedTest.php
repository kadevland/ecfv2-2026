<?php

declare(strict_types=1);

use App\Domain\Reservations\Events\ReservationCreated;
use App\Domain\Reservations\ValueObjects\ReservationId;

describe('ReservationCreated Event', function () {

    it('can be created with reservation ID', function () {
        $reservationId = ReservationId::generate();
        $event         = new ReservationCreated($reservationId);

        expect($event)->toBeInstanceOf(ReservationCreated::class);
        expect($event->reservationId)->toBe($reservationId);
    });

    it('has occurred at timestamp', function () {
        $reservationId = ReservationId::generate();
        $event         = new ReservationCreated($reservationId);

        expect($event->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
    });
});
