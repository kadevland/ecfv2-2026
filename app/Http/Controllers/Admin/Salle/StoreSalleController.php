<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Salle;

use Exception;
use App\Application\Bus\CommandBus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Salle\CreateSalleRequestMapper;
use App\Http\Requests\Admin\Salle\CreateSalleRequest;

final class StoreSalleController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(CreateSalleRequest $request): RedirectResponse
    {
        try {
            // 1. Mapper Request → Command
            $command = CreateSalleRequestMapper::toCommand($request);
            // dd('Step 1 - Command created:', $command);

            // 2. Validation métier dans la Command
            if (!$command->isValid()) {
                // dd('Step 2 - Command validation failed:', $command->validate());
                return back()
                    ->withErrors($command->validate())
                    ->withInput()
                    ->with('error', 'Erreur de validation des données métier');
            }
            // dd('Step 2 - Command validation OK');

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

            $salle = $result->getValue();

            return redirect()
                ->route('admin.salles.show', $salle->id->value)
                ->with('success', "Salle '{$salle->nom}' créée avec succès");

        } catch (Exception $e) {
            Log::error('StoreSalleController error', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()
                ->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])
                ->withInput();
        }
    }
}
