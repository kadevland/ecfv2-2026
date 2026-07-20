<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Exception;
use Illuminate\View\View;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Cinema\CreateCinemaRequestMapper;
use App\Http\Requests\Admin\Cinema\CreateCinemaRequest;

/**
 * Exemple de controller refactorisé avec CQRS et RequestMapper
 *
 * Montre l'utilisation des mappers pour découpler HTTP de Application
 */
final class CreateCinemaControllerExample extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {}

    /**
     * Affiche le formulaire de création d'un cinéma
     */
    public function create(): View
    {
        return view('admin.cinemas.create');
    }

    /**
     * Traite la création d'un cinéma
     */
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

            // 3. Dispatch vers le handler
            $commandResponse = $this->commandBus->dispatch($command);

            // 4. Redirection avec succès
            return redirect()
                ->route('admin.cinemas.show', $commandResponse->cinemaUuid)
                ->with('success', 'Cinéma créé avec succès !');

        } catch (Exception $e) {
            // 5. Gestion des erreurs
            return back()
                ->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])
                ->withInput();
        }
    }
}
