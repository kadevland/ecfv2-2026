<?php

declare(strict_types=1);

namespace App\Http\Mappers\Reservation;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Reservations\Queries\GetReservationsQuery;

/**
 * Mapper pour convertir Request en GetReservationsQuery
 */
final class GetReservationsListRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetReservationsQuery
     */
    public static function toQuery(Request $request): GetReservationsQuery
    {
        return new GetReservationsQuery(
            userId: $request->string('user_id')->toString() ?: null,
            seanceId: $request->string('seance_id')->toString() ?: null,
            statut: $request->string('statut')->toString() ?: null,
            numeroReservation: $request->string('numero_reservation')->toString() ?: null,
            dateFrom: $request->string('date_debut')->toString() ?: null,
            dateTo: $request->string('date_fin')->toString() ?: null,
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
            sortBy: $request->string('sort_by', 'created_at')->toString(),
            sortDirection: $request->string('sort_direction', 'desc')->toString(),
        );
    }
}
