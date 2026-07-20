<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPagination;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Http\Controllers\Contracts\PaginationControllerInterface;
use App\Application\Salle\Queries\GetSallesList\GetSallesListQuery;

final class ListSallesController extends Controller implements PaginationControllerInterface
{
    use HasPagination;

    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function __invoke(Request $request): View
    {
        try {
            // Préparer les filtres additionnels
            $additionalFilters = [];
            if ($request->string('cinema_id')->toString()) {
                $additionalFilters['cinema_id'] = $request->string('cinema_id')->toString();
            }

            $query = new GetSallesListQuery(
                page: $request->integer('page', 1),
                perPage: $request->integer('per_page', 20),
                search: $request->string('search')->toString() ?: null,
                technologies: $request->has('technologies') ? $request->input('technologies') : null,
                statut: $request->string('statut')->toString() ?: null,
                accessibilitePmr: $request->has('accessibilite_pmr') ? $request->boolean('accessibilite_pmr') : null,
                climatisation: $request->has('climatisation') ? $request->boolean('climatisation') : null,
                qualiteSon: $request->string('qualite_son')->toString() ?: null,
                tailleEcran: $request->string('taille_ecran')->toString() ?: null,
                typeEcran: $request->string('type_ecran')->toString() ?: null,
                sortBy: $request->string('sort_by', 'numero')->toString(),
                sortDirection: $request->string('sort_direction', 'asc')->toString(),
                filters: $additionalFilters ?: null,
            );

            if (!$query->isValid()) {
                abort(400, 'Paramètres de requête invalides');
            }

            $result = $this->queryBus->ask($query);

            if ($result->isError()) {
                abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des salles');
            }

            $response = $result->getValue();

            // Créer un LengthAwarePaginator avec le trait
            $pagination = $this->createPaginator($response, $request);

            $cinemas = $this->cinemaRepository->findAllForSelect();

            return view('admin.salles.index', [
                'salles'     => $response->getItems(),
                'total'      => $response->getTotal(),
                'pagination' => $pagination,
                'search'     => $request->string('search')->toString(),
                'cinemas'    => $cinemas,
                'filters'    => [
                    'technologies'      => $request->input('technologies'),
                    'statut'            => $request->string('statut')->toString(),
                    'accessibilite_pmr' => $request->boolean('accessibilite_pmr'),
                    'climatisation'     => $request->boolean('climatisation'),
                    'qualite_son'       => $request->string('qualite_son')->toString(),
                    'taille_ecran'      => $request->string('taille_ecran')->toString(),
                    'type_ecran'        => $request->string('type_ecran')->toString(),
                    'sort_by'           => $request->string('sort_by', 'numero')->toString(),
                    'sort_direction'    => $request->string('sort_direction', 'asc')->toString(),
                ],
            ]);

        } catch (Exception $e) {
            Log::error('ListSallesController error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Erreur lors de la récupération des salles : ' . $e->getMessage());
        }
    }
}
