<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPagination;
use App\Http\Mappers\Seance\GetSeancesListRequestMapper;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Http\Controllers\Contracts\PaginationControllerInterface;

final class ListSeancesController extends Controller implements PaginationControllerInterface
{
    use HasPagination;

    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function __invoke(Request $request): View
    {
        try {
            $query = GetSeancesListRequestMapper::toQuery($request);

            if (!$query->isValid()) {
                abort(400, 'Param�tres de requ�te invalides');
            }

            $result = $this->queryBus->ask($query);

            if ($result->isError()) {
                abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des séances');
            }

            $response = $result->getValue();

            // Créer un LengthAwarePaginator avec le trait
            $pagination = $this->createPaginator($response, $request);

            // Récupérer les films et salles qui ont des séances pour les filtres
            /** @phpstan-ignore-next-line method.notFound */
            $films = $this->filmRepository->findFilmsWithSeancesForSelect();
            /** @phpstan-ignore-next-line method.notFound */
            $salles = $this->salleRepository->findSallesWithSeancesForSelect();

            return view('admin.seances.index', [
                'seances'    => $response->getItems(),
                'total'      => $response->getTotal(),
                'pagination' => $pagination,
                'search'     => $request->string('search')->toString(),
                'films'      => $films,
                'salles'     => $salles,
                'filters'    => [
                    'film_id'         => $request->string('film_id')->toString(),
                    'salle_id'        => $request->string('salle_id')->toString(),
                    'cinema_id'       => $request->string('cinema_id')->toString(),
                    'date_debut'      => $request->string('date_debut')->toString(),
                    'date_fin'        => $request->string('date_fin')->toString(),
                    'version'         => $request->string('version')->toString(),
                    'technologies'    => $request->input('technologies'),
                    'statut'          => $request->string('statut')->toString(),
                    'seances_a_venir' => $request->boolean('seances_a_venir'),
                    'sort_by'         => $request->string('sort_by', 'date_heure_debut')->toString(),
                    'sort_direction'  => $request->string('sort_direction', 'asc')->toString(),
                ],
            ]);

        } catch (Exception $e) {
            abort(500, 'Erreur lors de la r�cup�ration des s�ances : ' . $e->getMessage());
        }
    }
}
