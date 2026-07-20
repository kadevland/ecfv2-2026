<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Application\Cinema\Commands\ToggleCinemaStatus\ToggleCinemaStatusCommand;

final class ToggleCinemaStatusController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(string $uuid): RedirectResponse
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
