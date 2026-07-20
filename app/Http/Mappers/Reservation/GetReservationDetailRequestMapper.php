<?php

declare(strict_types=1);

namespace App\Http\Mappers\Reservation;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Reservations\Queries\GetReservationDetailQuery;

/**
 * Mapper pour convertir Request en GetReservationDetailQuery
 */
final class GetReservationDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetReservationDetailQuery
     */
    public static function toQuery(Request $request, string $reservationUuid): GetReservationDetailQuery
    {
        return new GetReservationDetailQuery(
            reservationUuid: $reservationUuid,
        );
    }
}
