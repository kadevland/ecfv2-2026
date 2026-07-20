<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\Entities\Reservation;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\ReservationId;
use App\Domain\Cinema\ValueObjects\UtilisateurId;

describe('Reservation Entity - Real Coverage 100%', function () {
    it('constructor works correctly', function () {
        $reservation = new Reservation(
            ReservationId::generate(),
            SeanceId::generate(),
            UtilisateurId::generate(),
            2,
            new Money(2400, new Devise('EUR')),
            'confirmee',
            new \DateTime,
            new \DateTime
        );

        expect($reservation)->toBeInstanceOf(Reservation::class);
        expect($reservation->nombrePlaces)->toBe(2);
        expect($reservation->statut)->toBe('confirmee');
    });

    it('create method works correctly', function () {
        $reservationId = ReservationId::generate();
        $seanceId      = SeanceId::generate();
        $utilisateurId = UtilisateurId::generate();
        $montant       = new Money(1200, new Devise('EUR'));

        $reservation = Reservation::create(
            $reservationId,
            $seanceId,
            $utilisateurId,
            1,
            $montant
        );

        expect($reservation)->toBeInstanceOf(Reservation::class);
        expect($reservation->id)->toBe($reservationId);
        expect($reservation->seanceId)->toBe($seanceId);
        expect($reservation->utilisateurId)->toBe($utilisateurId);
        expect($reservation->nombrePlaces)->toBe(1);
        expect($reservation->montantTotal)->toBe($montant);
        expect($reservation->statut)->toBe('en_attente');
        expect($reservation->dateReservation)->toBeInstanceOf(\DateTime::class);
        expect($reservation->dateExpiration)->toBeInstanceOf(\DateTime::class);
    });

    it('create method with custom status', function () {
        $reservation = Reservation::create(
            ReservationId::generate(),
            SeanceId::generate(),
            UtilisateurId::generate(),
            3,
            new Money(3600, new Devise('EUR')),
            'confirmee'
        );

        expect($reservation->statut)->toBe('confirmee');
        expect($reservation->nombrePlaces)->toBe(3);
    });
});
