<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Cinema\UpdateCinemaRequestMapper;
use App\Http\Requests\Admin\Cinema\UpdateCinemaRequest;
use App\Http\Controllers\Traits\HandlesErrorsWithSweetAlert;

final class UpdateCinemaController extends Controller
{
    use HandlesErrorsWithSweetAlert;

    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateCinemaRequest $request, string $uuid): RedirectResponse
    {
        try {

            // 1. Mapper Request → Command
            $command = UpdateCinemaRequestMapper::toCommand($request, $uuid);

            // 2. Dispatch via CommandBus
            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                flash()->error($result->getErrorMessage());

                return back()->withInput();
            }

            flash()->success('Cinéma mis à jour avec succès');

            return redirect()->route('admin.cinemas.show', $uuid);

        } catch (Exception $e) {
            // Pour les erreurs critiques, utiliser SweetAlert2 avec retour forcé
            return $this->abort500WithSweetAlert($e,
                'Une erreur critique s\'est produite lors de la mise à jour du cinéma.'
            );
        }
    }
}
