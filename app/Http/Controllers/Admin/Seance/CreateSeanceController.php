<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Domain\Enums\VersionFilm;
use App\Http\Controllers\Controller;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Application\Seance\DTOs\SeanceDetailDto;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;

final class CreateSeanceController extends Controller
{
    public function __construct(
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function __invoke(Request $request): View
    {
        // film_id est OBLIGATOIRE
        $filmId = $request->string('film_id')->toString();
        if (empty($filmId)) {
            abort(400, 'film_id est requis pour créer une séance');
        }

        // Récupérer le film pour affichage (NON modifiable)
        $film = $this->filmRepository->findById(FilmId::fromString($filmId));
        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        // Récupérer les salles pour sélection
        $salles           = $this->salleRepository->findAllForSelect();
        $preSelectedSalle = $request->string('salle_id')->toString() ?: null;

        return view('admin.seances.create', [
            'film'               => $film,
            'salles'             => $salles,
            'preSelectedSalle'   => $preSelectedSalle,
            'versions'           => array_map(fn ($v) => ['value' => $v->value, 'label' => $v->label()], VersionFilm::cases()),
            'qualitesProjection' => QualiteProjection::cases(),
            'qualitesSonores'    => QualiteSonore::cases(),
            'statutsDisponibles' => SeanceDetailDto::getStatutsDisponibles(),
        ]);
    }
}
