<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Film;

use Exception;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Film\CreateFilmRequestMapper;
use App\Http\Requests\Admin\Film\CreateFilmRequest;

final class StoreFilmController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(CreateFilmRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $command = CreateFilmRequestMapper::toCommand($validated);

            $result = $this->commandBus->dispatch($command);

            if ($result->isError()) {
                $error   = $result->getError();
                $message = $result->getErrorMessage() ?: 'Erreur lors de la création';

                if (is_array($error)) {
                    return back()
                        ->withInput()
                        ->withErrors($error);
                }

                return back()
                    ->withInput()
                    ->withErrors(['general' => $message]);
            }

            $film = $result->getValue();

            return redirect()
                ->route('admin.films.show', $film->id->value)
                ->with('success', "Film '{$film->titre}' créé avec succès");

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }
}
