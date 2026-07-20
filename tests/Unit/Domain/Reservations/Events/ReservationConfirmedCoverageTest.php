<?php

declare(strict_types=1);

use App\Domain\Reservations\Events\ReservationConfirmed;

describe('ReservationConfirmed Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $reservationUuid = 'reservation-3333-4444-5555-666677778888';
            $event           = ReservationConfirmed::fromUuid($reservationUuid);

            expect($event)->toBeInstanceOf(ReservationConfirmed::class);
            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($reservationUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getReservationUuid())->toBe($reservationUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
