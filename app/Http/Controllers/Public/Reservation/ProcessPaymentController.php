<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Reservation;

use Illuminate\Http\Request;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Application\Reservations\Commands\ProcessPaymentCommand;

/**
 * Contrôleur pour traiter le paiement d'une réservation
 */
final class ProcessPaymentController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    /**
     * Traite le paiement d'une réservation (simulation)
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $reservationId = $request->input('reservation_id');
        $paymentMethod = $request->input('payment_method', 'carte_bancaire');
        $amount        = (int) $request->input('amount'); // en centimes

        if (empty($reservationId) || empty($amount)) {
            return redirect()->back()
                ->withErrors(['error' => 'Données de paiement invalides']);
        }

        // Créer la commande de paiement
        $command = new ProcessPaymentCommand(
            reservationId: $reservationId,
            paymentMethod: $paymentMethod,
            amount: $amount,
            detailsPaiement: [
                'simulation' => true,
                'timestamp'  => time(),
            ]
        );

        // Exécuter la commande
        $result = $this->commandBus->dispatch($command);

        if ($result->isError()) {
            return redirect()->back()
                ->withErrors(['error' => $result->getErrorMessage() ?? 'Erreur lors du paiement']);
        }

        $data = $result->getValue();

        return redirect()->route('reservation.confirmation')
            ->with('success', 'Paiement effectué avec succès !')
            ->with('transaction_id', $data['transaction_id'])
            ->with('reservation_id', $data['reservation_id']);
    }
}
