<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Application\Salle\DTOs\SalleFormDto;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class CreateSalleController extends Controller
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function __invoke(): View
    {
        $salleForm = SalleFormDto::empty();
        $cinemas   = $this->cinemaRepository->findAllForSelect();

        return view('admin.salles.create', [
            'salle'              => $salleForm,
            'cinemas'            => $cinemas,
            'qualitesProjection' => QualiteProjection::cases(),
            'qualitesSonore'     => QualiteSonore::cases(),
        ]);
    }
}
