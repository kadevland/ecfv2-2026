<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Film;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPagination;
use App\Http\Mappers\Film\GetFilmsListRequestMapper;
use App\Http\Controllers\Contracts\PaginationControllerInterface;

final class ListFilmsController extends Controller implements PaginationControllerInterface
{
    use HasPagination;

    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request): View
    {
        try {
            $query = GetFilmsListRequestMapper::toQuery($request);

            if (!$query->isValid()) {
                abort(400, 'Paramètres de requête invalides');
            }

            $result = $this->queryBus->ask($query);

            if ($result->isError()) {
                abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des films');
            }

            $response = $result->getValue();

            // Créer un LengthAwarePaginator avec le trait
            $pagination = $this->createPaginator($response, $request);

            return view('admin.films.index', [
                'films'      => $response->getItems(),
                'total'      => $response->getTotal(),
                'pagination' => $pagination,
                'search'     => $request->string('search')->toString(),
                'filters'    => [
                    'genres'         => $request->input('genres'),
                    'classification' => $request->string('classification')->toString(),
                    'en_salles'      => $request->boolean('en_salles'),
                    'prochainement'  => $request->boolean('prochainement'),
                    'sort_by'        => $request->string('sort_by', 'titre')->toString(),
                    'sort_direction' => $request->string('sort_direction', 'desc')->toString(),
                ],
            ]);

        } catch (Exception $e) {
            abort(500, 'Erreur lors de la récupération des films : ' . $e->getMessage());
        }
    }
}
