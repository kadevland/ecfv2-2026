<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutReservation: string
{
    case EN_ATTENTE = 'en_attente';
    case CONFIRMEE  = 'confirmee';
    case PAYEE      = 'payee';
    case ANNULEE    = 'annulee';
    case EXPIREE    = 'expiree';
    case UTILISEE   = 'utilisee';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::CONFIRMEE  => 'Confirmée',
            self::PAYEE      => 'Payée',
            self::ANNULEE    => 'Annulée',
            self::EXPIREE    => 'Expirée',
            self::UTILISEE   => 'Utilisée',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'Réservation créée, en attente de paiement',
            self::CONFIRMEE  => 'Réservation confirmée par le client',
            self::PAYEE      => 'Réservation payée et validée',
            self::ANNULEE    => 'Réservation annulée par le client ou le cinéma',
            self::EXPIREE    => 'Réservation expirée faute de paiement',
            self::UTILISEE   => 'Billets utilisés, séance terminée',
        };
    }

    public function isPending(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::CONFIRMEE]);
    }

    public function isValid(): bool
    {
        return $this === self::PAYEE;
    }

    public function isCancelled(): bool
    {
        return in_array($this, [self::ANNULEE, self::EXPIREE]);
    }

    public function isCompleted(): bool
    {
        return $this === self::UTILISEE;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::CONFIRMEE, self::PAYEE]);
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::CONFIRMEE]);
    }

    public function requiresPayment(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::CONFIRMEE]);
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'text-yellow-600',
            self::CONFIRMEE  => 'text-blue-600',
            self::PAYEE      => 'text-green-600',
            self::ANNULEE    => 'text-red-600',
            self::EXPIREE    => 'text-gray-600',
            self::UTILISEE   => 'text-purple-600',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'bg-yellow-100 text-yellow-800',
            self::CONFIRMEE  => 'bg-blue-100 text-blue-800',
            self::PAYEE      => 'bg-green-100 text-green-800',
            self::ANNULEE    => 'bg-red-100 text-red-800',
            self::EXPIREE    => 'bg-gray-100 text-gray-800',
            self::UTILISEE   => 'bg-purple-100 text-purple-800',
        };
    }

    /**
     * @return array<StatutReservation>
     */
    public function getNextPossibleStates(): array
    {
        return match ($this) {
            self::EN_ATTENTE => [self::CONFIRMEE, self::ANNULEE, self::EXPIREE],
            self::CONFIRMEE  => [self::PAYEE, self::ANNULEE, self::EXPIREE],
            self::PAYEE      => [self::UTILISEE, self::ANNULEE],
            self::ANNULEE, self::EXPIREE, self::UTILISEE => [], // États terminaux
        };
    }
}
