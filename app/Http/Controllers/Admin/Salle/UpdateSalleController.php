<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Salle\UpdateSalleRequestMapper;
use App\Http\Requests\Admin\Salle\UpdateSalleRequest;
use App\Http\Controllers\Traits\HandlesErrorsWithSweetAlert;

final class UpdateSalleController extends Controller
{
    use HandlesErrorsWithSweetAlert;

    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateSalleRequest $request, string $uuid): RedirectResponse
    {
        try {
            // 1. Créer la Command depuis la Request (plomberie HTTP)
            $command = UpdateSalleRequestMapper::toCommand($request, $uuid);

            // 2. Dispatch via CommandBus (délégation)
            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                flash()->error($result->getErrorMessage());

                return back()->withInput();
            }

            flash()->success('Salle mise à jour avec succès');

            return redirect()->route('admin.salles.show', $uuid);

        } catch (Exception $e) {
            // Pour les erreurs critiques, utiliser SweetAlert2 avec retour forcé
            return $this->abort500WithSweetAlert($e,
                'Une erreur critique s\'est produite lors de la mise à jour de la salle.'
            );
        }
    }
}
