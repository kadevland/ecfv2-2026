<?php

declare(strict_types=1);

namespace App\Application\Reservations\Handlers;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Application\Contracts\Result;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Contracts\CommandInterface;
use App\Domain\Reservations\Entities\Reservation;
use App\Application\Contracts\CommandHandlerInterface;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Application\Reservations\Commands\SendReservationEmailCommand;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

/**
 * Handler pour envoyer les emails de réservation
 * Implémente ShouldQueue pour traitement asynchrone
 */
final class SendReservationEmailHandler implements CommandHandlerInterface, ShouldQueue
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof SendReservationEmailCommand) {
            return Result::error('INVALID_COMMAND', 'Command type not supported');
        }

        try {
            // Récupérer la réservation
            $reservationId = new ReservationId($command->reservationId);
            $reservation   = $this->reservationRepository->findById($reservationId);

            if (!$reservation) {
                return Result::error(
                    error: 'RESERVATION_NOT_FOUND',
                    message: 'Réservation introuvable'
                );
            }

            // Simulation d'envoi d'email (pour l'ECF)
            $emailSent = $this->simulateEmailSending($command, $reservation);

            if (!$emailSent) {
                return Result::error(
                    error: 'EMAIL_SEND_FAILED',
                    message: 'Erreur lors de l\'envoi de l\'email'
                );
            }

            // Logger l'envoi pour traçabilité
            Log::info('Email de réservation envoyé', [
                'reservation_id'     => $reservation->id->value,
                'numero_reservation' => $reservation->numeroReservation,
                'email_type'         => $command->emailType,
                'user_id'            => $reservation->userId->value,
            ]);

            return Result::success([
                'email_type'     => $command->emailType,
                'reservation_id' => $reservation->id->value,
                'sent_at'        => now()->toISOString(),
                'message'        => 'Email envoyé avec succès',
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'email de réservation', [
                'reservation_id' => $command->reservationId,
                'email_type'     => $command->emailType,
                'error'          => $e->getMessage(),
            ]);

            return Result::error(
                error: 'SYSTEM_ERROR',
                message: 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage()
            );
        }
    }

    /**
     * Simule l'envoi d'email pour l'ECF
     * En production, ici on utiliserait Laravel Mail avec des templates
     */
    private function simulateEmailSending(SendReservationEmailCommand $command, Reservation $reservation): bool
    {
        // Simulation d'envoi avec délai
        usleep(rand(100000, 500000)); // 0.1 à 0.5 seconde

        // 98% de succès
        if (rand(1, 100) <= 98) {
            // Dans une vraie implémentation :
            // Mail::to($reservation->user->email)
            //     ->queue(new ReservationConfirmationMail($reservation));

            return true;
        }

        return false;
    }
}
