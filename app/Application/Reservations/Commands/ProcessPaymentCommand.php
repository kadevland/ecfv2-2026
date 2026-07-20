<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class ProcessPaymentCommand implements CommandInterface
{
    /**
     * @param array<string, mixed>|null $detailsPaiement infos carte/paypal selon méthode
     */
    public function __construct(
        public readonly string $reservationId,
        public readonly string $paymentMethod, // carte, paypal, especes
        public readonly int $amount, // en centimes
        /** @var array<string, mixed>|null */ public readonly ?array $detailsPaiement = null, // infos carte/paypal selon méthode
        public readonly ?string $codePromo = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reservationId: $data['reservation_id'],
            paymentMethod: $data['payment_method'] ?? $data['methode_paiement'] ?? 'card',
            amount: $data['amount'] ?? $data['montant'],
            detailsPaiement: $data['details_paiement'] ?? null,
            codePromo: $data['code_promo'] ?? null,
        );
    }
}
