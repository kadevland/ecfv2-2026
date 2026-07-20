<?php

declare(strict_types=1);

use App\Domain\Reservations\Entities\Reservation;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Application\Reservations\Commands\ProcessPaymentCommand;
use App\Application\Reservations\Handlers\ProcessPaymentHandler;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

beforeEach(function () {
    $this->reservationRepository = Mockery::mock(ReservationRepositoryInterface::class);

    $this->handler = new ProcessPaymentHandler(
        $this->reservationRepository
    );

    // Générer un UUID valide pour éviter les erreurs de validation
    $this->validUuid = '01936284-7c4e-7bb8-b3f4-1a2b3c4d5e6f';
    $this->command   = new ProcessPaymentCommand(
        reservationId: $this->validUuid,
        paymentMethod: 'card',
        amount: 2500
    );

    $this->reservation   = Mockery::mock(Reservation::class);
    $this->reservationId = new ReservationId($this->validUuid);
});

describe('ProcessPaymentHandler', function () {
    it('processes payment successfully', function () {
        // Arrange
        $this->reservation->shouldReceive('markAsPaid')->once();
        $this->reservation->id = $this->reservationId;

        $this->reservationRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->reservation);
        $this->reservationRepository
            ->shouldReceive('save')
            ->once()
            ->with($this->reservation);

        // Act
        $result = $this->handler->handle($this->command);

        // Assert
        expect($result->isSuccess())->toBeTrue();
        expect($result->getValue())->toHaveKey('transaction_id');
        expect(strlen($result->getValue()['transaction_id']))->toBe(12);
    });

    it('returns error when reservation not found', function () {
        // Arrange
        $this->reservationRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        // Act
        $result = $this->handler->handle($this->command);

        // Assert
        expect($result->isSuccess())->toBeFalse();
        expect($result->getError())->toBe('ReservationNotFound');
        expect($result->getErrorMessage())->toBe('Réservation non trouvée');
    });

    it('generates unique transaction ids', function () {
        // Arrange
        $this->reservation->shouldReceive('markAsPaid')->twice();
        $this->reservation->id = $this->reservationId;

        $this->reservationRepository
            ->shouldReceive('findById')
            ->twice()
            ->andReturn($this->reservation);
        $this->reservationRepository
            ->shouldReceive('save')
            ->twice()
            ->with($this->reservation);

        // Act
        $result1 = $this->handler->handle($this->command);
        $result2 = $this->handler->handle($this->command);

        // Assert
        expect($result1->isSuccess())->toBeTrue();
        expect($result2->isSuccess())->toBeTrue();
        expect($result1->getValue()['transaction_id'])
            ->not->toBe($result2->getValue()['transaction_id']);
    });
});
