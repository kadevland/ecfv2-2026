<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Reservation;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Mappers\Reservation\GetReservationsListRequestMapper;

final class ListReservationsController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request): View
    {
        // 1. Mapper Request → Query
        $query = GetReservationsListRequestMapper::toQuery($request);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération des réservations');
        }

        $paginatedResult = $result->getValue();

        return view('admin.reservations.index', [
            'reservations' => $paginatedResult->items,
            'total'        => $paginatedResult->total,
            'pagination'   => $paginatedResult,
            'filters'      => $request->all(['user_id', 'seance_id', 'statut', 'numero_reservation', 'date_debut', 'date_fin']),
        ]);
    }
}
