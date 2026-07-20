<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Seance\UpdateSeanceRequestMapper;
use App\Http\Requests\Admin\Seance\UpdateSeanceRequest;

final class UpdateSeanceController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateSeanceRequest $request, string $uuid): RedirectResponse
    {
        try {
            // 1. Mapper Request � Command
            $command = UpdateSeanceRequestMapper::toCommand($request, $uuid);

            // 2. Validation m�tier dans la Command
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

            return redirect()
                ->route('admin.seances.show', $uuid)
                ->with('success', 'Séance mise à jour avec succès');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()])
                ->withInput();
        }
    }
}
