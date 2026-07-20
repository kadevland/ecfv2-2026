<?php

declare(strict_types=1);

namespace App\Http\Mappers\Salle;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQuery;

/**
 * Mapper pour convertir Request en GetSalleDetailQuery
 */
final class GetSalleDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request HTTP en GetSalleDetailQuery
     */
    public static function toQuery(Request $request, string $salleUuid): GetSalleDetailQuery
    {
        return new GetSalleDetailQuery(
            salleUuid: $salleUuid,
        );
    }
}
