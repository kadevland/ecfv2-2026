<?php

declare(strict_types=1);

use App\Application\Reservations\DTOs\UserReservationDto;

beforeEach(function () {
    $futureDate       = (new \DateTime)->add(new \DateInterval('P1D')); // Tomorrow
    $futureExpiration = (new \DateTime)->add(new \DateInterval('P2D')); // Day after tomorrow

    $this->baseData = [
        'id'                => 'res-123',
        'numeroReservation' => 'RES-2025-001',
        'statut'            => 'confirmee',
        'nombrePlaces'      => 2,
        'montantTotal'      => 2500, // 25.00€ en centimes
        'dateReservation'   => (new \DateTime)->format('Y-m-d H:i:s'),
        'dateExpiration'    => $futureExpiration->format('Y-m-d H:i:s'),
        'commentaires'      => null,
        'filmTitre'         => 'Avatar 3',
        'cinemaName'        => 'Cinéphoria Paris',
        'salleNom'          => 'Salle 1',
        'dateHeureDebut'    => $futureDate->format('Y-m-d H:i:s'),
        'userName'          => 'John Doe',
        'userEmail'         => 'john@example.com',
    ];
});

it('creates dto from array correctly', function () {
    $dto = UserReservationDto::fromArray($this->baseData);

    expect($dto->id)->toBe('res-123');
    expect($dto->numeroReservation)->toBe('RES-2025-001');
    expect($dto->statut)->toBe('confirmee');
    expect($dto->nombrePlaces)->toBe(2);
    expect($dto->montantTotal)->toBe(2500);
    expect($dto->filmTitre)->toBe('Avatar 3');
});

it('returns true for active reservation when confirmed', function () {
    $data = array_merge($this->baseData, ['statut' => 'confirmee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->isActive())->toBeTrue();
});

it('returns true for active reservation when paid', function () {
    $data = array_merge($this->baseData, ['statut' => 'payee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->isActive())->toBeTrue();
});

it('returns false for active reservation when cancelled', function () {
    $data = array_merge($this->baseData, ['statut' => 'annulee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->isActive())->toBeFalse();
});

it('returns false for active reservation when expired', function () {
    $expiredDate = (new \DateTime)->sub(new \DateInterval('P1D')); // Yesterday
    $data        = array_merge($this->baseData, [
        'statut'         => 'confirmee',
        'dateExpiration' => $expiredDate->format('Y-m-d H:i:s'),
    ]);
    $dto = UserReservationDto::fromArray($data);

    expect($dto->isActive())->toBeFalse();
});

it('returns false for past seance', function () {
    $pastDate = (new \DateTime)->sub(new \DateInterval('P1D')); // Yesterday
    $data     = array_merge($this->baseData, [
        'dateHeureDebut' => $pastDate->format('Y-m-d H:i:s'),
    ]);
    $dto = UserReservationDto::fromArray($data);

    expect($dto->isPastSeance())->toBeTrue();
});

it('returns true for future seance', function () {
    $futureDate = (new \DateTime)->add(new \DateInterval('P1D')); // Tomorrow
    $data       = array_merge($this->baseData, [
        'dateHeureDebut' => $futureDate->format('Y-m-d H:i:s'),
    ]);
    $dto = UserReservationDto::fromArray($data);

    expect($dto->isPastSeance())->toBeFalse();
});

it('returns correct status badge class for confirmed status', function () {
    $data = array_merge($this->baseData, ['statut' => 'confirmee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->getStatusBadgeClass())->toBe('bg-yellow-100 text-yellow-800');
});

it('returns correct status badge class for paid status', function () {
    $data = array_merge($this->baseData, ['statut' => 'payee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->getStatusBadgeClass())->toBe('bg-green-100 text-green-800');
});

it('returns correct status badge class for cancelled status', function () {
    $data = array_merge($this->baseData, ['statut' => 'annulee']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->getStatusBadgeClass())->toBe('bg-red-100 text-red-800');
});

it('returns correct status badge class for default status', function () {
    $data = array_merge($this->baseData, ['statut' => 'en_attente_paiement']);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->getStatusBadgeClass())->toBe('bg-gray-100 text-gray-800');
});

it('formats amount correctly', function () {
    $data = array_merge($this->baseData, ['montantTotal' => 1250]);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->getFormattedAmount())->toBe('12,50 €');
});

it('handles null expiration date', function () {
    $data = array_merge($this->baseData, ['dateExpiration' => null]);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->isActive())->toBeTrue();
});

it('handles null commentaires', function () {
    $data = array_merge($this->baseData, ['commentaires' => null]);
    $dto  = UserReservationDto::fromArray($data);

    expect($dto->commentaires)->toBeNull();
});

it('formats dates correctly when created from array', function () {
    $dto = UserReservationDto::fromArray($this->baseData);

    expect($dto->dateReservation)->toBeInstanceOf(\DateTime::class);
    expect($dto->dateHeureDebut)->toBeInstanceOf(\DateTime::class);
});
