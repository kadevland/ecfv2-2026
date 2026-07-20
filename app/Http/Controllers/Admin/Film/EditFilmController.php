<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Film;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Mappers\Film\GetFilmDetailRequestMapper;

final class EditFilmController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetFilmDetailRequestMapper::toQueryForEdit($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            if ($result->getError() === 'FILM_NOT_FOUND') {
                abort(404, 'Film non trouvé');
            }
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération du film');
        }

        $film = $result->getValue();

        return view('admin.films.edit', [
            'film' => $film,
        ]);
    }
}
