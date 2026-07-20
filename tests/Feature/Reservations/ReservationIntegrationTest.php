<?php

declare(strict_types=1);

use App\Application\Bus\QueryBus;
use App\Domain\Film\Entities\Film;
use App\Domain\User\Entities\User;
use App\Application\Bus\CommandBus;
use App\Domain\Cinema\Entities\Salle;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Reservations\Entities\Reservation;
use App\Application\Reservations\Commands\ProcessPaymentCommand;
use App\Application\Reservations\Queries\GetUserReservationsQuery;
use App\Application\Reservations\Commands\CreateReservationCommand;
use App\Application\Reservations\Queries\GetReservationByNumberQuery;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->commandBus = app(CommandBus::class);
    $this->queryBus   = app(QueryBus::class);

    // Create test data
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'name'  => 'Test User',
    ]);

    $this->cinema = Cinema::factory()->create([
        'nom'   => 'Test Cinema',
        'ville' => 'Test City',
    ]);

    $this->salle = Salle::factory()->create([
        'cinema_id' => $this->cinema->id->value,
        'nom'       => 'Salle 1',
        'capacite'  => 100,
    ]);

    $this->film = Film::factory()->create([
        'titre' => 'Test Film',
        'duree' => 120,
    ]);

    $this->seance = Seance::factory()->create([
        'film_id'          => $this->film->id->value,
        'salle_id'         => $this->salle->id->value,
        'date_heure_debut' => now()->addDays(2),
        'date_heure_fin'   => now()->addDays(2)->addHours(2),
    ]);
});

describe('Complete Reservation Flow', function () {

    it('creates reservation successfully with valid data', function () {
        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $this->seance->id->value,
            nombrePlaces: 3,
            commentaires: 'Test reservation'
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeTrue();
        expect($result->getData())->toBeString(); // Should return reservation ID

        // Verify reservation exists in database
        $this->assertDatabaseHas('reservations', [
            'user_id'       => $this->user->id->value,
            'seance_id'     => $this->seance->id->value,
            'nombre_places' => 3,
            'statut'        => 'confirmee',
        ]);
    });

    it('fails to create reservation with insufficient seats', function () {
        // First, fill up most seats
        for ($i = 0; $i < 10; $i++) {
            Reservation::factory()->create([
                'seance_id'     => $this->seance->id->value,
                'nombre_places' => 9,
                'statut'        => 'confirmee',
            ]);
        }

        // Now try to reserve more than available
        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $this->seance->id->value,
            nombrePlaces: 15 // Should fail - not enough seats
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ValidationError');
        expect($result->getMessage())->toContain('places disponibles');
    });

    it('processes payment successfully after reservation', function () {
        // First create a reservation
        $createCommand = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $this->seance->id->value,
            nombrePlaces: 2
        );

        $createResult = $this->commandBus->dispatch($createCommand);
        expect($createResult->isSuccess())->toBeTrue();

        $reservationId = $createResult->getData();

        // Then process payment (keep trying until success due to 95% rate)
        $paymentSuccessful = false;
        $maxAttempts       = 50;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $paymentCommand = new ProcessPaymentCommand(
                reservationId: $reservationId,
                paymentMethod: 'card',
                amount: 2500
            );

            $paymentResult = $this->commandBus->dispatch($paymentCommand);

            if ($paymentResult->isSuccess()) {
                $paymentSuccessful = true;
                expect($paymentResult->getData())->toHaveKey('transaction_id');
                break;
            }
        }

        expect($paymentSuccessful)->toBeTrue();

        // Verify reservation status updated
        $this->assertDatabaseHas('reservations', [
            'id'     => $reservationId,
            'statut' => 'payee',
        ]);
    });

    it('retrieves user reservations correctly', function () {
        // Create multiple reservations for the user
        $reservation1 = Reservation::factory()->create([
            'user_id'       => $this->user->id->value,
            'seance_id'     => $this->seance->id->value,
            'statut'        => 'confirmee',
            'nombre_places' => 2,
        ]);

        $reservation2 = Reservation::factory()->create([
            'user_id'       => $this->user->id->value,
            'seance_id'     => $this->seance->id->value,
            'statut'        => 'payee',
            'nombre_places' => 1,
        ]);

        // Create reservation for another user (should not appear)
        $otherUser = User::factory()->create();
        Reservation::factory()->create([
            'user_id'   => $otherUser->id->value,
            'seance_id' => $this->seance->id->value,
            'statut'    => 'confirmee',
        ]);

        $query = new GetUserReservationsQuery(
            userId: $this->user->id->value
        );

        $result = $this->queryBus->dispatch($query);

        expect($result->isSuccess())->toBeTrue();
        $data = $result->getData();

        expect($data)->toHaveKey('reservations');
        expect($data)->toHaveKey('pagination');
        expect(count($data['reservations']))->toBe(2);

        // Check that only user's reservations are returned
        $reservationIds = array_column($data['reservations'], 'id');
        expect($reservationIds)->toContain($reservation1->id->value);
        expect($reservationIds)->toContain($reservation2->id->value);
    });

    it('finds reservation by number correctly', function () {
        $reservation = Reservation::factory()->create([
            'user_id'            => $this->user->id->value,
            'seance_id'          => $this->seance->id->value,
            'numero_reservation' => 'RES-TEST-123',
            'statut'             => 'payee',
        ]);

        $query = new GetReservationByNumberQuery(
            numeroReservation: 'RES-TEST-123'
        );

        $result = $this->queryBus->dispatch($query);

        expect($result->isSuccess())->toBeTrue();
        $data = $result->getData();

        expect($data['id'])->toBe($reservation->id->value);
        expect($data['numeroReservation'])->toBe('RES-TEST-123');
        expect($data['statut'])->toBe('payee');
    });

    it('handles past seance validation', function () {
        // Create a seance in the past
        $pastSeance = Seance::factory()->create([
            'film_id'          => $this->film->id->value,
            'salle_id'         => $this->salle->id->value,
            'date_heure_debut' => now()->subHours(2),
            'date_heure_fin'   => now()->subMinutes(30),
        ]);

        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $pastSeance->id->value,
            nombrePlaces: 2
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ValidationError');
        expect($result->getMessage())->toContain('séance passée');
    });

    it('handles too close to seance time validation', function () {
        // Create a seance starting in 30 minutes (less than 1 hour minimum)
        $soonSeance = Seance::factory()->create([
            'film_id'          => $this->film->id->value,
            'salle_id'         => $this->salle->id->value,
            'date_heure_debut' => now()->addMinutes(30),
            'date_heure_fin'   => now()->addMinutes(150),
        ]);

        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $soonSeance->id->value,
            nombrePlaces: 2
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ValidationError');
        expect($result->getMessage())->toContain('minimum 1 heure');
    });

    it('handles multi-tariff pricing correctly', function () {
        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $this->seance->id->value,
            placesByTarif: [
                'normal' => 2,
                'reduit' => 1,
                'enfant' => 1,
            ]
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeTrue();

        // Verify reservation created with correct total places
        $this->assertDatabaseHas('reservations', [
            'user_id'       => $this->user->id->value,
            'seance_id'     => $this->seance->id->value,
            'nombre_places' => 4, // 2 + 1 + 1
            'statut'        => 'confirmee',
        ]);
    });
});

describe('Error Handling', function () {

    it('handles non-existent seance', function () {
        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: 'non-existent-seance',
            nombrePlaces: 2
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('SeanceNotFound');
    });

    it('handles invalid number of places', function () {
        $command = new CreateReservationCommand(
            userId: $this->user->id->value,
            seanceId: $this->seance->id->value,
            nombrePlaces: 15 // Exceeds maximum of 10
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ValidationError');
        expect($result->getMessage())->toContain('Maximum 10 places');
    });

    it('handles payment for non-existent reservation', function () {
        $command = new ProcessPaymentCommand(
            reservationId: 'non-existent-reservation',
            paymentMethod: 'card',
            amount: 2500
        );

        $result = $this->commandBus->dispatch($command);

        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ReservationNotFound');
    });
});

afterEach(function () {
    Mockery::close();
});
