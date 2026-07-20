<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQuery;
use App\Application\Cinema\Queries\GetCinemaDetail\GetCinemaDetailQuery;
use App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQueryHandler;
use App\Application\Cinema\Queries\GetCinemaDetail\GetCinemaDetailQueryHandler;

final class CinemaController extends Controller
{
    public function __construct(
        private readonly GetCinemasListQueryHandler $getCinemasListHandler,
        private readonly GetCinemaDetailQueryHandler $getCinemaDetailHandler,
    ) {}

    public function index(Request $request): View
    {
        $query = new GetCinemasListQuery(
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 12),
            location: $request->string('location')->toString() ?: null,
            filters: $request->has('filters') ? $request->get('filters') : null
        );

        $result = $this->getCinemasListHandler->handle($query);

        if ($result->isError()) {
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des cinémas');
        }

        $paginatedResult = $result->getValue();

        return view('public.cinemas.index', [
            'cinemas'     => $paginatedResult->cinemas,
            'total'       => $paginatedResult->total,
            'currentPage' => $paginatedResult->page,
            'perPage'     => $paginatedResult->perPage,
            'totalPages'  => (int) ceil($paginatedResult->total / $paginatedResult->perPage),
            'filters'     => $request->all(['location', 'filters']),
        ]);
    }

    public function show(string $uuid): View
    {
        $query  = new GetCinemaDetailQuery($uuid);
        $result = $this->getCinemaDetailHandler->handle($query);

        if ($result->isError()) {
            abort(404, 'Cinéma non trouvé');
        }

        $cinema = $result->getValue();

        return view('public.cinemas.show', [
            'cinema' => $cinema,
        ]);
    }

    public function api_index(Request $request): JsonResponse
    {
        $query = new GetCinemasListQuery(
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
            location: $request->string('location')->toString() ?: null,
            filters: $request->has('filters') ? $request->get('filters') : null
        );

        $result = $this->getCinemasListHandler->handle($query);

        if ($result->isError()) {
            return response()->json([
                'error' => $result->getErrorMessage(),
            ], 500);
        }

        $response = $result->getValue();

        return response()->json([
            'data' => $response->cinemas,
            'meta' => [
                'total'        => $response->total,
                'current_page' => $response->page,
                'per_page'     => $response->perPage,
                'total_pages'  => (int) ceil($response->total / $response->perPage),
            ],
        ]);
    }

    public function api_show(string $uuid): JsonResponse
    {
        $query  = new GetCinemaDetailQuery($uuid);
        $result = $this->getCinemaDetailHandler->handle($query);

        if ($result->isError()) {
            return response()->json([
                'error' => 'Cinéma non trouvé',
            ], 404);
        }

        $cinema = $result->getValue();

        return response()->json([
            'data' => $cinema,
        ]);
    }
}
