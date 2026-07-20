<?php

declare(strict_types=1);

namespace App\Http\Mappers\Cinema;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Cinema\Queries\GetCinemaDetail\GetCinemaDetailQuery;

/**
 * Mapper pour convertir Request en GetCinemaDetailQuery
 */
final class GetCinemaDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request HTTP en GetCinemaDetailQuery
     */
    public static function toQuery(Request $request, string $cinemaUuid): GetCinemaDetailQuery
    {
        // Récupération des paramètres optionnels d'inclusion
        $includeSalles  = self::toBool($request->query('include_salles', false));
        $includeSeances = self::toBool($request->query('include_seances', false));

        return new GetCinemaDetailQuery(
            cinemaUuid: $cinemaUuid,
            includeSalles: $includeSalles,
            includeSeances: $includeSeances,
        );
    }
}
