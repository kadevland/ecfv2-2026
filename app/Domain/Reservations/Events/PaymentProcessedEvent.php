<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Events;

use DateTime;
use DateTimeInterface;
use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Reservations\ValueObjects\ReservationId;

/**
 * Événement déclenché quand un paiement est traité
 */
final class PaymentProcessedEvent extends DomainEvent
{
    public readonly DateTimeInterface $processedAt;

    public function __construct(
        public readonly ReservationId $reservationId,
        public readonly string $paymentMethod,
        public readonly int $amount,
        public readonly string $transactionId,
        ?DateTimeInterface $processedAt = null,
    ) {
        parent::__construct();
        $this->processedAt = $processedAt ?? new DateTime;
    }

    public function getAggregateId(): string
    {
        return $this->reservationId->value;
    }

    public function getAggregateType(): string
    {
        return 'reservation';
    }

    public function getEventName(): string
    {
        return 'payment.processed';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_id'       => $this->getEventId(),
            'event_name'     => $this->getEventName(),
            'aggregate_id'   => $this->getAggregateId(),
            'aggregate_type' => $this->getAggregateType(),
            'occurred_on'    => $this->getOccurredOn()->format('Y-m-d H:i:s'),
            'event_data'     => $this->eventData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function eventData(): array
    {
        return [
            'reservation_id' => $this->reservationId->value,
            'payment_method' => $this->paymentMethod,
            'amount'         => $this->amount,
            'transaction_id' => $this->transactionId,
            'processed_at'   => $this->processedAt->format('Y-m-d H:i:s'),
        ];
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->processedAt;
    }

    public function eventType(): string
    {
        return 'reservation.payment.processed';
    }
}
