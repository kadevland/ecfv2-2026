<?php

declare(strict_types=1);

namespace App\Application\Reservations\Listeners;

use Exception;
use App\Application\Bus\CommandBus;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Domain\Reservations\Events\PaymentProcessedEvent;
use App\Application\Reservations\Commands\SendReservationEmailCommand;

/**
 * Listener qui déclenche l'envoi d'email de confirmation de paiement
 * Traité en asynchrone via les queues
 */
final class SendPaymentConfirmationEmailListener implements ShouldQueue
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    /**
     * Gère l'événement de paiement traité
     */
    public function handle(PaymentProcessedEvent $event): void
    {
        try {
            // Créer la commande d'envoi d'email
            $command = SendReservationEmailCommand::paymentConfirmation(
                reservationId: $event->reservationId->value,
                transactionId: $event->transactionId
            );

            // Dispatcher la commande (sera traitée en asynchrone)
            $this->commandBus->dispatch($command);

        } catch (Exception $e) {
            // Logger l'erreur mais ne pas faire échouer le processus principal
            \Illuminate\Support\Facades\Log::error('Erreur lors du déclenchement d\'email de confirmation de paiement', [
                'reservation_id' => $event->reservationId->value,
                'transaction_id' => $event->transactionId,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
