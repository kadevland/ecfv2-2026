<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Cinema\CreateCinemaRequestMapper;
use App\Http\Requests\Admin\Cinema\CreateCinemaRequest;

final class StoreCinemaController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(CreateCinemaRequest $request): RedirectResponse
    {
        try {
            // 1. Mapper Request → Command
            $command = CreateCinemaRequestMapper::toCommand($request);

            //dump($command);

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
                ->route('admin.cinemas.show', $cinema->id->value)
                ->with('success', "Cinéma '{$cinema->nom}' créé avec succès");

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])
                ->withInput();
        }
    }
}
