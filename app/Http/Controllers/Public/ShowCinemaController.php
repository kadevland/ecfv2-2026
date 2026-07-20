<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Application\Bus\PublicQueryBus;
use App\Application\Public\Cinema\Queries\GetPublicCinemaDetail\GetPublicCinemaDetailQuery;

final class ShowCinemaController extends Controller
{
    public function __construct(
        private readonly PublicQueryBus $publicQueryBus,
    ) {}

    public function __invoke(string $uuid): View
    {
        // 1. Créer la Query CQRS
        $query = new GetPublicCinemaDetailQuery($uuid);

        // 2. Dispatch via PublicQueryBus (architecture CQRS/DDD MongoDB)
        $result = $this->publicQueryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Cinéma non trouvé');
        }

        $response = $result->getValue();
        $cinema   = $response->cinema;

        return view('public.cinemas.show', [
            'cinema' => $cinema,
        ]);
    }
}
