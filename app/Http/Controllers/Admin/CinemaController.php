<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Traits\CreatesPaginationTrait;
use App\Application\Cinema\DTOs\CinemaFormDto;
use App\Http\Mappers\Cinema\CreateCinemaRequestMapper;
use App\Http\Mappers\Cinema\UpdateCinemaRequestMapper;
use App\Http\Requests\Admin\Cinema\CreateCinemaRequest;
use App\Http\Requests\Admin\Cinema\UpdateCinemaRequest;
use App\Http\Mappers\Cinema\GetCinemasListRequestMapper;
use App\Http\Mappers\Cinema\GetCinemaDetailRequestMapper;
use App\Application\Cinema\Commands\ToggleCinemaStatus\ToggleCinemaStatusCommand;

final class CinemaController extends Controller
{
    use CreatesPaginationTrait;

    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): View
    {
        // 1. Mapper Request → Query
        $query = GetCinemasListRequestMapper::toQuery($request);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des cinémas');
        }

        $queryResponse = $result->getValue();

        // 3. Créer LengthAwarePaginator via le Trait générique
        $paginatedCinemas = $this->createPaginator($queryResponse, $request);

        return view('admin.cinemas.index', [
            'cinemas' => $paginatedCinemas,
            'filters' => $request->all(['location', 'filters']),
        ]);
    }

    public function show(Request $request, string $uuid): View
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

    public function create(): View
    {
        $cinemaForm = CinemaFormDto::empty();

        return view('admin.cinemas.create', [
            'cinema' => $cinemaForm,
        ]);
    }

    public function store(CreateCinemaRequest $request): RedirectResponse
    {
        try {
            // 1. Mapper Request → Command
            $command = CreateCinemaRequestMapper::toCommand($request);

            // 2. Validation métier dans la Command
            if (!$command->isValid()) {
                return back()
                    ->withErrors($command->validate())
                    ->withInput();
            }

            // 3. Dispatch via CommandBus
            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                $error   = $result->getError();
                $message = $result->getErrorMessage() ?: 'Erreur lors de la création';

                // Si l'erreur est un tableau de validation, l'utiliser directement
                if (is_array($error)) {
                    return back()
                        ->withInput()
                        ->withErrors($error);
                }

                return back()
                    ->withInput()
                    ->withErrors(['general' => $message]);
            }

            $cinema = $result->getValue();

            return redirect()
                ->route('admin.cinemas.show', $cinema->id->value())
                ->with('success', "Cinéma '{$cinema->nom}' créé avec succès");

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetCinemaDetailRequestMapper::toQuery($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, $result->getErrorMessage() ?: 'Cinéma non trouvé');
        }

        $cinema     = $result->getValue();
        $cinemaForm = CinemaFormDto::fromDetailDto($cinema);

        return view('admin.cinemas.edit', [
            'cinema' => $cinemaForm,
            'uuid'   => $uuid,
        ]);
    }

    public function update(UpdateCinemaRequest $request, string $uuid): RedirectResponse
    {
        try {
            // 1. Mapper Request → Command
            $command = UpdateCinemaRequestMapper::toCommand($request, $uuid);

            // 2. Validation métier dans la Command
            if (!$command->isValid()) {
                return back()
                    ->withErrors($command->validate())
                    ->withInput();
            }

            // 3. Dispatch via CommandBus
            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                $error   = $result->getError();
                $message = $result->getErrorMessage() ?: 'Erreur lors de la mise à jour';

                // Si l'erreur est un tableau de validation, l'utiliser directement
                if (is_array($error)) {
                    return back()
                        ->withInput()
                        ->withErrors($error);
                }

                return back()
                    ->withInput()
                    ->withErrors(['general' => $message]);
            }

            $data   = $result->getValue();
            $cinema = $data['cinema'];

            return redirect()
                ->route('admin.cinemas.show', $uuid)
                ->with('success', "Cinéma '{$cinema->nom}' mis à jour avec succès");

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function toggleStatus(string $uuid): RedirectResponse
    {
        try {
            // 1. Créer Command directement (pas de mapping nécessaire)
            $command = new ToggleCinemaStatusCommand($uuid);

            // 2. Dispatch via CommandBus
            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                $message = $result->getErrorMessage() ?: 'Erreur lors du changement de statut';

                return back()
                    ->withErrors(['general' => $message]);
            }

            $data   = $result->getValue();
            $cinema = $data['cinema'];
            $action = $data['action'];

            return redirect()
                ->route('admin.cinemas.show', $uuid)
                ->with('success', "Cinéma '{$cinema->nom}' $action avec succès");

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors du changement de statut : ' . $e->getMessage()]);
        }
    }
}
