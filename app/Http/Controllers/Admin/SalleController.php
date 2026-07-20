<?php

declare(strict_types=1);

// declare(strict_types=1);

// namespace App\Http\Controllers\Admin;

// use Illuminate\View\View;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\Rules\SalleCapacityCoherence;
// use Illuminate\Http\RedirectResponse;
// use App\Application\Cinema\Commands\CreateSalleCommand;
// use App\Application\Cinema\Handlers\CreateSalleHandler;
// use App\Application\Cinema\Queries\GetSalles\GetSallesQuery;
// use App\Application\Cinema\Queries\GetSalles\GetSallesQueryHandler;
// use App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQuery;
// use App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQueryHandler;

// final class SalleController extends Controller
// {
//     public function __construct(
//         private readonly GetSallesQueryHandler $getSallesHandler,
//         private readonly CreateSalleHandler $createSalleHandler,
//         private readonly GetSalleDetailQueryHandler $getSalleDetailHandler,
//     ) {}

//     public function index(Request $request): View
//     {
//         $query = new GetSallesQuery(
//             cinemaId: $request->string('cinema_uuid')->toString() ?: null,
//             statut: $request->string('statut')->toString() ?: null,
//             technologies: $request->has('technologies') ? $request->get('technologies') : null,
//             accessibilitePmr: $request->has('accessibilite_pmr') ? (bool) $request->get('accessibilite_pmr') : null,
//             page: $request->integer('page', 1),
//             perPage: $request->integer('per_page', 20),
//             sortBy: $request->string('sort_by', 'nom')->toString(),
//             sortDirection: $request->string('sort_direction', 'asc')->toString(),
//         );

//         $result = $this->getSallesHandler->handle($query);

//         if ($result->isError()) {
//             abort(500, $result->getErrorMessage() ?: 'Erreur lors de la r�cup�ration des salles');
//         }

//         $paginatedResult = $result->getValue();

//         // Get cinemas for filter dropdown - méthode optimisée
//         $cinemas = app(\App\Domain\Cinema\Repositories\CinemaRepositoryInterface::class)->findAllForSelect();

//         return view('admin.salles.index', [
//             'salles'     => $paginatedResult->items,
//             'total'      => $paginatedResult->total,
//             'pagination' => $paginatedResult,
//             'cinemas'    => $cinemas,
//             'filters'    => $request->all(['cinema_uuid', 'statut', 'technologies', 'accessibilite_pmr']),
//         ]);
//     }

//     public function create(Request $request): View
//     {
//         // Get cinemas for dropdown - méthode optimisée
//         $cinemas = app(\App\Domain\Cinema\Repositories\CinemaRepositoryInterface::class)->findAllForSelect();

//         $preSelectedCinema = $request->string('cinema_uuid')->toString() ?: null;

//         return view('admin.salles.create', [
//             'cinemas'           => $cinemas,
//             'preSelectedCinema' => $preSelectedCinema,
//             'qualitesProjection' => \App\Domain\Cinema\Enums\QualiteProjection::cases(),
//             'qualitesSonore'     => \App\Domain\Cinema\Enums\QualiteSonore::cases(),
//         ]);
//     }

//     public function store(Request $request): RedirectResponse
//     {
//         $validated = $request->validate([
//             'cinema_uuid'       => 'required|string|exists:cinema.cinemas,uuid',
//             'nom'               => 'required|string|min:2|max:100',
//             'capacite_totale'   => ['required', 'integer', 'min:10', 'max:1000', new SalleCapacityCoherence],
//             'nombre_rangees'    => 'required|integer|min:1|max:50',
//             'places_par_rangee' => 'required|integer|min:1|max:50',
//             'places_standard'   => 'nullable|integer|min:0',
//             'places_pmr'        => 'nullable|integer|min:0',
//             'qualites_video'    => 'nullable|array',
//             'qualites_video.*'  => 'string|in:2K,4K,IMAX,DOLBY_VISION,3D,LASER',
//             'qualites_audio'    => 'nullable|array',
//             'qualites_audio.*'  => 'string|in:5.1,7.1,DOLBY_ATMOS,DTS_X,IMAX_SOUND',
//             'climatisation'     => 'boolean',
//             'accessibilite_pmr' => 'boolean',
//             'statut'            => 'required|string|in:ACTIVE,MAINTENANCE,RENOVATION,HORS_SERVICE',
//             'est_active'        => 'boolean',
//         ]);

//         $command = CreateSalleCommand::fromArray($validated);
//         $result  = $this->createSalleHandler->handle($command);

//         if ($result->isError()) {
//             $error   = $result->getError();
//             $message = $result->getErrorMessage() ?: 'Erreur lors de la cr�ation';

//             if (is_array($error)) {
//                 return back()
//                     ->withInput()
//                     ->withErrors($error);
//             }

//             return back()
//                 ->withInput()
//                 ->withErrors(['general' => $message]);
//         }

//         $salle = $result->getValue();

//         return redirect()
//             ->route('admin.salles.index', ['cinema_uuid' => $validated['cinema_uuid']])
//             ->with('success', "Salle '{$salle->nom}' cr��e avec succ�s");
//     }

//     public function show(string $uuid): View
//     {
//         $query = new GetSalleDetailQuery(
//             salleUuid: $uuid,
//             includeSeances: false,
//             includeMaintenances: false
//         );

//         $result = $this->getSalleDetailHandler->handle($query);

//         if ($result->isError()) {
//             abort(404, $result->getErrorMessage() ?: 'Salle non trouvée');
//         }

//         $salle = $result->getValue();

//         return view('admin.salles.show', [
//             'salle' => $salle,
//         ]);
//     }

//     public function edit(string $uuid): View
//     {
//         $query = new GetSalleDetailQuery(
//             salleUuid: $uuid,
//             includeSeances: false,
//             includeMaintenances: false
//         );

//         $result = $this->getSalleDetailHandler->handle($query);

//         if ($result->isError()) {
//             abort(404, $result->getErrorMessage() ?: 'Salle non trouvée');
//         }

//         $salle = $result->getValue();

//         // Get cinemas for dropdown - méthode optimisée
//         $cinemas = app(\App\Domain\Cinema\Repositories\CinemaRepositoryInterface::class)->findAllForSelect();

//         return view('admin.salles.edit', [
//             'salle'   => $salle,
//             'cinemas' => $cinemas,
//         ]);
//     }

//     public function update(Request $request, string $uuid): RedirectResponse
//     {
//         $validated = $request->validate([
//             'cinema_uuid'       => 'required|string|exists:cinema.cinemas,uuid',
//             'nom'               => 'required|string|min:2|max:100',
//             'capacite_totale'   => ['required', 'integer', 'min:10', 'max:1000', new SalleCapacityCoherence],
//             'nombre_rangees'    => 'required|integer|min:1|max:50',
//             'places_par_rangee' => 'required|integer|min:1|max:50',
//             'places_standard'   => 'nullable|integer|min:0',
//             'places_pmr'        => 'nullable|integer|min:0',
//             'qualites_video'    => 'nullable|array',
//             'qualites_video.*'  => 'string|in:2K,4K,IMAX,DOLBY_VISION,3D,LASER',
//             'qualites_audio'    => 'nullable|array',
//             'qualites_audio.*'  => 'string|in:5.1,7.1,DOLBY_ATMOS,DTS_X,IMAX_SOUND',
//             'climatisation'     => 'boolean',
//             'accessibilite_pmr' => 'boolean',
//             'statut'            => 'required|string|in:ACTIVE,MAINTENANCE,RENOVATION,HORS_SERVICE',
//             'est_active'        => 'boolean',
//         ]);

//
//         // Pour l'instant, redirection avec message d'information
//         return redirect()
//             ->route('admin.salles.show', $uuid)
//             ->with('info', 'Mise à jour des salles à implémenter');
//     }
// }
