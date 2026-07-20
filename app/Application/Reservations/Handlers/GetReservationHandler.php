<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Application\Reservations\Queries\GetReservationQuery;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

final readonly class GetReservationHandler implements QueryHandlerInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetReservationQuery) {
            return Result::error('InvalidQuery', 'Query invalide');
        }

        if (!$query->isValid()) {
            return Result::error(
                'INVALID_QUERY',
                'Query invalide'
            );
        }

        try {
            $reservation = null;

            // Chercher par ID ou par numéro de réservation
            if (!empty($query->reservationId)) {
                $reservation = $this->reservationRepository->findById(
                    ReservationId::fromString($query->reservationId)
                );
            } elseif (!empty($query->numeroReservation)) {
                $reservation = $this->reservationRepository->findByNumero($query->numeroReservation);
            }

            if (!$reservation) {
                return Result::error('ReservationNotFound', 'Réservation non trouvée');
            }

            return Result::success($reservation);
        } catch (Exception $e) {
            return Result::error('UnexpectedError', $e->getMessage());
        }
    }
}
