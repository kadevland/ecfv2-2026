<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Account;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class TestReservationsController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Test minimal avec données en dur
        $fakeReservations = [
            (object) [
                'numero_reservation' => 'RES251205F60B46',
                'statut'             => 'EN_ATTENTE_PAIEMENT',
                'film_titre'         => 'Test Film 1',
                'cinema_nom'         => 'Test Cinema',
                'montant_total'      => 2500,
                'created_at'         => now(),
            ],
            (object) [
                'numero_reservation' => 'RES25120510326C',
                'statut'             => 'EN_ATTENTE_PAIEMENT',
                'film_titre'         => 'Test Film 2',
                'cinema_nom'         => 'Test Cinema',
                'montant_total'      => 1950,
                'created_at'         => now()->subHour(),
            ],
        ];

        $fakePaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $fakeReservations,
            2,
            15,
            1,
            ['path' => request()->url()]
        );

        return view('public.account.reservations', [
            'user' => (object) [
                'id'     => $user->id,
                'email'  => 'test@example.com',
                'type'   => $user->type,
                'prenom' => 'Test',
                'nom'    => 'User',
            ],
            'reservations'   => $fakePaginator,
            'activeCount'    => 2,
            'pastCount'      => 0,
            'allCount'       => 2,
            'selectedStatus' => $request->query('statut'),
        ]);
    }
}
