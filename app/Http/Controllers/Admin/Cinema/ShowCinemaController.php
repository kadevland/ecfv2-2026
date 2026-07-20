<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Mappers\Cinema\GetCinemaDetailRequestMapper;

final class ShowCinemaController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetCinemaDetailRequestMapper::toQuery($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Cinéma non trouvé');
        }

        $cinema = $result->getValue();

        return view('admin.cinemas.show', [
            'cinema' => $cinema,
        ]);
    }
}
