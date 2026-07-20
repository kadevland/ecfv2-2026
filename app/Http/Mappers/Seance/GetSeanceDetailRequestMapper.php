<?php

declare(strict_types=1);

namespace App\Http\Mappers\Seance;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Seance\Queries\GetSeanceDetail\GetSeanceDetailQuery;

/**
 * Mapper pour convertir Request en GetSeanceDetailQuery
 */
final class GetSeanceDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetSeanceDetailQuery pour l'affichage
     */
    public static function toQuery(Request $request, string $seanceUuid): GetSeanceDetailQuery
    {
        return new GetSeanceDetailQuery(
            seanceUuid: $seanceUuid,
            includeReservations: $request->boolean('include_reservations', false),
            includePlacesOccupees: $request->boolean('include_places_occupees', false)
        );
    }

    /**
     * Convertit une Request en GetSeanceDetailQuery pour l'édition
     */
    public static function toQueryForEdit(Request $request, string $seanceUuid): GetSeanceDetailQuery
    {
        return new GetSeanceDetailQuery(
            seanceUuid: $seanceUuid,
            includeReservations: false,
            includePlacesOccupees: false
        );
    }
}
