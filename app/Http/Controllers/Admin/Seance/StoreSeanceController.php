<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Seance;

use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Mappers\Seance\CreateSeanceRequestMapper;
use App\Http\Requests\Admin\Seance\CreateSeanceRequest;

final class StoreSeanceController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function __invoke(CreateSeanceRequest $request): RedirectResponse
    {
        // Conversion via mapper suivant le pattern architectural
        $command = CreateSeanceRequestMapper::toCommand($request);

        $result = $this->commandBus->dispatch($command);

        if ($result->isSuccess()) {
            return redirect()
                ->route('admin.seances.index')
                ->with('success', 'Séance créée avec succès');
        } else {
            return back()
                ->withInput()
                ->withErrors(['general' => $result->getErrorMessage() ?: 'Erreur lors de la création de la séance']);
        }
    }
}
