<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Application\Users\DTOs\ClientFormDto;
use App\Http\Mappers\Users\GetUserDetailRequestMapper;

final class EditClientController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetUserDetailRequestMapper::toQueryForEdit($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            if ($result->getError() === 'USER_NOT_FOUND') {
                abort(404, 'Client non trouvé');
            }
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération du client');
        }

        $client = $result->getValue();

        // Vérifier que c'est bien un client
        if (!$client->isClient()) {
            abort(404, 'Client non trouvé');
        }

        $clientForm = ClientFormDto::fromDetailDto($client);

        return view('admin.users.clients.edit', [
            'client' => $clientForm,
            'uuid'   => $uuid,
        ]);
    }
}
