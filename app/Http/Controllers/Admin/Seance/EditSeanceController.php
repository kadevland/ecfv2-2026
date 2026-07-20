<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Domain\Enums\VersionFilm;
use App\Http\Controllers\Controller;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Application\Seance\DTOs\SeanceFormDto;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Seance\DTOs\SeanceDetailDto;
use App\Http\Mappers\Seance\GetSeanceDetailRequestMapper;

final class EditSeanceController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetSeanceDetailRequestMapper::toQueryForEdit($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Séance non trouvée');
        }

        $seanceDetail = $result->getValue();

        $seanceForm = SeanceFormDto::fromDetailDto($seanceDetail);

        return view('admin.seances.edit', [
            'seance'             => $seanceForm,
            'seanceDetail'       => $seanceDetail,
            'uuid'               => $uuid,
            'statutsDisponibles' => SeanceDetailDto::getStatutsDisponibles(),
            'versions'           => array_map(fn ($v) => ['value' => $v->value, 'label' => $v->label()], VersionFilm::cases()),
            'qualitesProjection' => array_map(fn ($q) => ['value' => $q->value, 'label' => $q->getLabel()], QualiteProjection::cases()),
            'qualitesSonore'     => array_map(fn ($q) => ['value' => $q->value, 'label' => $q->getLabel()], QualiteSonore::cases()),
        ]);
    }
}
