<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Application\Users\Queries\GetClientProfilsQuery;
use App\Application\Users\Queries\GetClientProfils\GetClientProfilsQueryHandler;

final class ListClientsController extends Controller
{
    public function __construct(
        private readonly GetClientProfilsQueryHandler $queryHandler,
    ) {}

    public function __invoke(Request $request): Response
    {
        // OPTIMISATION CQRS: Requête directe sur user_profils !
        $query = GetClientProfilsQuery::create(
            page: (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 20),
            search: $request->get('search'),
            estActif: $request->has('active') ? $request->boolean('active') : null
        );

        $result = $this->queryHandler->handle($query);

        if ($result->isError()) {
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des clients');
        }

        $paginatedCollection = $result->getValue();

        return response()->view('admin.users.clients.index', [
            'clients' => $paginatedCollection,
            'search'  => $request->get('search', ''),
        ]);
    }
}
