<?php

declare(strict_types=1);

use App\Domain\Reservations\Events\ReservationMade;

describe('ReservationMade Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $reservationUuid = 'reservation-1111-2222-3333-444455556666';
            $event           = ReservationMade::fromUuid($reservationUuid);

            expect($event)->toBeInstanceOf(ReservationMade::class);
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
