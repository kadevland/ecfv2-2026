<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Contracts\CommandInterface;

/**
 * Commande pour envoyer un email de confirmation de réservation
 * Cette commande sera traitée de manière asynchrone via les queues
 */
final readonly class SendReservationEmailCommand implements CommandInterface
{
    /**
     * @param array<string, mixed> $additionalData
     */
    public function __construct(
        public string $reservationId,
        public string $emailType = 'confirmation', // confirmation, payment, cancellation
        /** @var array<string, mixed> */ public array $additionalData = [],
    ) {}

    /**
     * Factory methods pour différents types d'emails
     */
    public static function confirmation(string $reservationId): self
    {
        return new self(
            reservationId: $reservationId,
            emailType: 'confirmation'
        );
    }

    public static function paymentConfirmation(string $reservationId, string $transactionId): self
    {
        return new self(
            reservationId: $reservationId,
            emailType: 'payment',
            additionalData: ['transaction_id' => $transactionId]
        );
    }

    public static function cancellation(string $reservationId, string $reason = ''): self
    {
        return new self(
            reservationId: $reservationId,
            emailType: 'cancellation',
            additionalData: ['reason' => $reason]
        );
    }

    public function isValid(): bool
    {
        return !empty($this->reservationId)
            && in_array($this->emailType, ['confirmation', 'payment', 'cancellation', 'reminder']);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];
        if (empty($this->reservationId)) {
            $errors['reservationId'] = 'L\'identifiant de réservation est requis';
        }
        if (!in_array($this->emailType, ['confirmation', 'payment', 'cancellation', 'reminder'])) {
            $errors['emailType'] = 'Type d\'email invalide';
        }

        return $errors;
    }
}
