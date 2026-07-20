<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use Exception;
use DomainException;
use App\Application\Contracts\Result;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Reservations\Events\PaymentProcessedEvent;
use App\Application\Reservations\Commands\ProcessPaymentCommand;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

/**
 * Handler pour traiter le paiement d'une réservation (simulation)
 */
final class ProcessPaymentHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof ProcessPaymentCommand) {
            return Result::error('InvalidCommand', 'Command type not supported');
        }

        try {
            // Récupérer la réservation
            $reservationId = ReservationId::fromString($command->reservationId);
            $reservation   = $this->reservationRepository->findById($reservationId);

            if (!$reservation) {
                return Result::error('ReservationNotFound', 'Réservation non trouvée');
            }

            // Dans un vrai projet, ici on appellerait l'API de paiement (Stripe, PayPal, etc.)
            // Pour l'ECF, on considère que le paiement réussit toujours
            // $paymentSuccess = $this->paymentGateway->process($command);

            // Mettre à jour le statut de la réservation
            $reservation->markAsPaid();

            // Sauvegarder
            $this->reservationRepository->save($reservation);

            // Générer un ID de transaction
            $transactionId = $this->generateTransactionId();

            // Déclencher l'événement de paiement
            $event = new PaymentProcessedEvent(
                reservationId: $reservation->id,
                paymentMethod: $command->paymentMethod,
                amount: $command->amount,
                transactionId: $transactionId
            );

            // $this->eventDispatcher->dispatch($event);

            return Result::success(['transaction_id' => $transactionId]);

        } catch (Exception $e) {
            // Re-throw domain exceptions pour les tests
            if ($e instanceof DomainException) {
                throw $e;
            }

            return Result::error('UnexpectedError', 'Erreur lors du traitement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Génère un ID de transaction unique
     */
    private function generateTransactionId(): string
    {
        return substr(bin2hex(random_bytes(6)), 0, 12);
    }
}
