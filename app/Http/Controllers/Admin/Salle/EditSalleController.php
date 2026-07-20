<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Salle\Queries\GetSalleForEdit\GetSalleForEditQuery;

final class EditSalleController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Créer la Query pour l'édition
        $query = new GetSalleForEditQuery($uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Salle non trouvée');
        }

        $salleEdit = $result->getValue();

        return view('admin.salles.edit', [
            'salle'              => $salleEdit,
            'uuid'               => $uuid,
            'qualitesProjection' => QualiteProjection::cases(),
            'qualitesSonore'     => QualiteSonore::cases(),
        ]);
    }
}
