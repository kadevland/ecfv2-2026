<?php

declare(strict_types=1);

use App\Domain\Reservations\ValueObjects\ReservationId;

describe('ReservationId ValueObject', function () {

    it('génère un ID unique', function () {
        $reservationId = ReservationId::generate();

        expect($reservationId->value)->toBeString();
        expect(strlen($reservationId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid          = 'reservation-uuid-123';
        $reservationId = ReservationId::fromString($uuid);

        expect($reservationId->value)->toBe($uuid);
        expect((string) $reservationId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'reservation-uuid-123';
        $id1  = ReservationId::fromString($uuid);
        $id2  = ReservationId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = ReservationId::fromString('reservation-1');
        $id2 = ReservationId::fromString('reservation-2');

        expect($id1->equals($id2))->toBeFalse();
    });
});
