<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Mappers\Seance\GetSeanceDetailRequestMapper;

final class ShowSeanceController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetSeanceDetailRequestMapper::toQuery($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Séance non trouvée');
        }

        $seance = $result->getValue();

        return view('admin.seances.show', [
            'seance' => $seance,
        ]);
    }
}
