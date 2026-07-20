<?php

declare(strict_types=1);

namespace App\Http\Mappers\Users;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Users\Queries\GetUserDetailQuery;

/**
 * Mapper pour convertir Request en GetUserDetailQuery
 */
final class GetUserDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetUserDetailQuery pour l'affichage
     */
    public static function toQuery(Request $request, string $userUuid): GetUserDetailQuery
    {
        return new GetUserDetailQuery(
            userUuid: $userUuid,
            includeProfile: $request->boolean('include_profile', true),
            includeReservations: $request->boolean('include_reservations', false)
        );
    }

    /**
     * Convertit une Request en GetUserDetailQuery pour l'édition
     */
    public static function toQueryForEdit(Request $request, string $userUuid): GetUserDetailQuery
    {
        return new GetUserDetailQuery(
            userUuid: $userUuid,
            includeProfile: true,
            includeReservations: false
        );
    }
}
