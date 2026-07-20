<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Traits\CreatesPaginationTrait;
use App\Http\Mappers\Cinema\GetCinemasListRequestMapper;

final class ListCinemasController extends Controller
{
    use CreatesPaginationTrait;

    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request): View
    {
        // 1. Mapper Request → Query
        $query = GetCinemasListRequestMapper::toQuery($request);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des cinémas');
        }

        $queryResponse = $result->getValue();

        // 3. Créer LengthAwarePaginator via le Trait générique
        $paginatedCinemas = $this->createPaginator($queryResponse, $request);

        return view('admin.cinemas.index', [
            'cinemas' => $paginatedCinemas,
            'filters' => $request->all(['location', 'pays']),
        ]);
    }
}
