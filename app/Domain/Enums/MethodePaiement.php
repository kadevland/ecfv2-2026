<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum MethodePaiement: string
{
    case CARTE_BANCAIRE = 'carte_bancaire';
    case PAYPAL         = 'paypal';
    case ESPECES        = 'especes';
    case VIREMENT       = 'virement';
    case CHEQUE         = 'cheque';
    case WALLET         = 'wallet';
    case CRYPTO         = 'crypto';

    public function label(): string
    {
        return match ($this) {
            self::CARTE_BANCAIRE => 'Carte bancaire',
            self::PAYPAL         => 'PayPal',
            self::ESPECES        => 'Espèces',
            self::VIREMENT       => 'Virement bancaire',
            self::CHEQUE         => 'Chèque',
            self::WALLET         => 'Portefeuille électronique',
            self::CRYPTO         => 'Cryptomonnaie',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CARTE_BANCAIRE => 'Paiement par carte bancaire (Visa, Mastercard, etc.)',
            self::PAYPAL         => 'Paiement via compte PayPal',
            self::ESPECES        => 'Paiement en espèces au guichet',
            self::VIREMENT       => 'Virement bancaire SEPA',
            self::CHEQUE         => 'Paiement par chèque bancaire',
            self::WALLET         => 'Apple Pay, Google Pay, Samsung Pay',
            self::CRYPTO         => 'Bitcoin, Ethereum, autres cryptomonnaies',
        };
    }

    public function isOnline(): bool
    {
        return in_array($this, [
            self::CARTE_BANCAIRE,
            self::PAYPAL,
            self::VIREMENT,
            self::WALLET,
            self::CRYPTO,
        ]);
    }

    public function isPhysical(): bool
    {
        return in_array($this, [self::ESPECES, self::CHEQUE]);
    }

    public function isInstant(): bool
    {
        return in_array($this, [
            self::CARTE_BANCAIRE,
            self::PAYPAL,
            self::ESPECES,
            self::WALLET,
            self::CRYPTO,
        ]);
    }

    public function requiresValidation(): bool
    {
        return in_array($this, [self::VIREMENT, self::CHEQUE]);
    }

    public function hasTransactionFees(): bool
    {
        return in_array($this, [
            self::CARTE_BANCAIRE,
            self::PAYPAL,
            self::CRYPTO,
        ]);
    }

    public function getTypicalProcessingTime(): string
    {
        return match ($this) {
            self::CARTE_BANCAIRE => 'Immédiat',
            self::PAYPAL         => 'Immédiat',
            self::ESPECES        => 'Immédiat',
            self::VIREMENT       => '1-3 jours ouvrés',
            self::CHEQUE         => '3-7 jours ouvrés',
            self::WALLET         => 'Immédiat',
            self::CRYPTO         => '10-60 minutes',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            self::CARTE_BANCAIRE => 'credit-card',
            self::PAYPAL         => 'paypal',
            self::ESPECES        => 'cash',
            self::VIREMENT       => 'bank',
            self::CHEQUE         => 'receipt',
            self::WALLET         => 'wallet',
            self::CRYPTO         => 'currency-bitcoin',
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::CARTE_BANCAIRE => 'text-blue-600',
            self::PAYPAL         => 'text-yellow-600',
            self::ESPECES        => 'text-green-600',
            self::VIREMENT       => 'text-purple-600',
            self::CHEQUE         => 'text-gray-600',
            self::WALLET         => 'text-indigo-600',
            self::CRYPTO         => 'text-orange-600',
        };
    }

    public function isAvailableOnline(): bool
    {
        return $this->isOnline();
    }

    public function isAvailableAtCounter(): bool
    {
        return in_array($this, [
            self::CARTE_BANCAIRE,
            self::ESPECES,
            self::CHEQUE,
        ]);
    }
}
