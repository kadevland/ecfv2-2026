<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Bus\PublicQueryBus;
use App\Http\ViewModels\Public\Cinema\CinemaListItemViewModel;
use App\Http\Mappers\Public\Cinema\GetPublicCinemasListRequestMapper;

final class ListCinemasController extends Controller
{
    public function __construct(
        private readonly PublicQueryBus $publicQueryBus,
    ) {}

    public function __invoke(Request $request): View
    {
        // 1. Mapper Request → Public Query
        $query = GetPublicCinemasListRequestMapper::toQuery($request);

        // 2. Dispatch via PublicQueryBus (MongoDB)
        $result = $this->publicQueryBus->ask($query);

        if ($result->isError()) {
            // En cas d'erreur, utiliser des données par défaut pour éviter une page cassée
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('public.cinemas.index', [
                'cinemas' => $emptyPaginator,
                'filters' => $request->only(['location']),
                'error'   => 'Erreur lors du chargement des cinémas. Veuillez réessayer.',
            ]);
        }

        // 3. Récupérer le paginator avec DTOs directement du query handler (architecture CQRS)
        $paginator = $result->getValue();

        // 4. Transformer les DTOs en ViewModels pour la logique d'affichage
        $paginator->getCollection()->transform(function ($cinemaDto) {
            return new CinemaListItemViewModel($cinemaDto);
        });

        // 5. Configurer les liens de pagination
        $paginator->withQueryString();

        return view('public.cinemas.index', [
            'cinemas' => $paginator,
            'filters' => $request->only(['location']),
        ]);
    }
}
