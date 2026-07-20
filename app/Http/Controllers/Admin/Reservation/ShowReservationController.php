<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Reservation;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Http\Controllers\Controller;
use App\Http\Mappers\Reservation\GetReservationDetailRequestMapper;

final class ShowReservationController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request, string $uuid): View
    {
        // 1. Mapper Request → Query
        $query = GetReservationDetailRequestMapper::toQuery($request, $uuid);

        // 2. Dispatch via QueryBus
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            if ($result->getError() === 'RESERVATION_NOT_FOUND') {
                abort(404, 'Réservation introuvable');
            }
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération de la réservation');
        }

        $reservation = $result->getValue();
        //dd($reservation);

        return view('admin.reservations.show', [
            'reservation' => $reservation,
        ]);
    }
}
