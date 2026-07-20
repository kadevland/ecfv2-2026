<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Contracts\QueryInterface;

final readonly class GetReservationsQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $userId = null,
        public readonly ?string $seanceId = null,
        public readonly ?string $statut = null,
        public readonly ?string $numeroReservation = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
    ) {}

    public static function all(): self
    {
        return new self;
    }

    public static function byUser(string $userId): self
    {
        return new self(userId: $userId);
    }

    public static function bySeance(string $seanceId): self
    {
        return new self(seanceId: $seanceId);
    }

    public static function byStatut(string $statut): self
    {
        return new self(statut: $statut);
    }

    public static function byNumero(string $numeroReservation): self
    {
        return new self(numeroReservation: $numeroReservation);
    }

    public static function pending(): self
    {
        return new self(statut: 'en_attente');
    }

    public static function confirmed(): self
    {
        return new self(statut: 'confirmee');
    }

    public function withPagination(int $page, int $perPage): self
    {
        return new self(
            userId: $this->userId,
            seanceId: $this->seanceId,
            statut: $this->statut,
            numeroReservation: $this->numeroReservation,
            dateFrom: $this->dateFrom,
            dateTo: $this->dateTo,
            page: $page,
            perPage: $perPage,
            sortBy: $this->sortBy,
            sortDirection: $this->sortDirection,
        );
    }
}
