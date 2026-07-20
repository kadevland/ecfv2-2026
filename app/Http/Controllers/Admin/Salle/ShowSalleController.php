<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Salle\GetSalleDetailRequestMapper;
use App\Http\Controllers\Traits\HandlesErrorsWithSweetAlert;

final class ShowSalleController extends Controller
{
    use HandlesErrorsWithSweetAlert;

    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View|RedirectResponse
    {
        // 1. Mapper Request → Query
        $query = GetSalleDetailRequestMapper::toQuery($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            // Utiliser SweetAlert2 au lieu d'abort() pour une meilleure UX
            return match ($result->getError()) {
                'SALLE_NOT_FOUND' => $this->abort404WithSweetAlert(
                    'La salle demandée n\'existe pas ou a été supprimée.'
                ),
                'ACCESS_DENIED' => $this->abort403WithSweetAlert(
                    'Vous n\'avez pas l\'autorisation de consulter cette salle.'
                ),
                default => $this->abort500WithSweetAlert(
                    null,
                    'Impossible de récupérer les informations de la salle.'
                )
            };
        }

        $salle = $result->getValue();

        return view('admin.salles.show', [
            'salle' => $salle,
        ]);
    }
}
