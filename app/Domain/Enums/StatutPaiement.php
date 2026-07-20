<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutPaiement: string
{
    case EN_ATTENTE = 'en_attente';
    case AUTORISE   = 'autorise';
    case CAPTURE    = 'capture';
    case ECHEC      = 'echec';
    case REMBOURSE  = 'rembourse';
    case ANNULE     = 'annule';
    case EXPIRE     = 'expire';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::AUTORISE   => 'Autorisé',
            self::CAPTURE    => 'Capturé',
            self::ECHEC      => 'Échec',
            self::REMBOURSE  => 'Remboursé',
            self::ANNULE     => 'Annulé',
            self::EXPIRE     => 'Expiré',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'Paiement en cours de traitement',
            self::AUTORISE   => 'Paiement autorisé par la banque',
            self::CAPTURE    => 'Paiement capturé et finalisé',
            self::ECHEC      => 'Paiement refusé ou échoué',
            self::REMBOURSE  => 'Paiement remboursé au client',
            self::ANNULE     => 'Paiement annulé avant capture',
            self::EXPIRE     => 'Autorisation expirée sans capture',
        };
    }

    public function isPending(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::AUTORISE]);
    }

    public function isSuccessful(): bool
    {
        return $this === self::CAPTURE;
    }

    public function isFailed(): bool
    {
        return in_array($this, [self::ECHEC, self::ANNULE, self::EXPIRE]);
    }

    public function isRefunded(): bool
    {
        return $this === self::REMBOURSE;
    }

    public function canBeRefunded(): bool
    {
        return $this === self::CAPTURE;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::AUTORISE]);
    }

    public function canBeCaptured(): bool
    {
        return $this === self::AUTORISE;
    }

    public function requiresAction(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::AUTORISE]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::CAPTURE,
            self::ECHEC,
            self::REMBOURSE,
            self::ANNULE,
            self::EXPIRE,
        ]);
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'text-yellow-600',
            self::AUTORISE   => 'text-blue-600',
            self::CAPTURE    => 'text-green-600',
            self::ECHEC      => 'text-red-600',
            self::REMBOURSE  => 'text-orange-600',
            self::ANNULE     => 'text-gray-600',
            self::EXPIRE     => 'text-gray-500',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'bg-yellow-100 text-yellow-800',
            self::AUTORISE   => 'bg-blue-100 text-blue-800',
            self::CAPTURE    => 'bg-green-100 text-green-800',
            self::ECHEC      => 'bg-red-100 text-red-800',
            self::REMBOURSE  => 'bg-orange-100 text-orange-800',
            self::ANNULE     => 'bg-gray-100 text-gray-800',
            self::EXPIRE     => 'bg-gray-50 text-gray-600',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'clock',
            self::AUTORISE   => 'shield-check',
            self::CAPTURE    => 'check-circle',
            self::ECHEC      => 'x-circle',
            self::REMBOURSE  => 'arrow-left-circle',
            self::ANNULE     => 'minus-circle',
            self::EXPIRE     => 'clock-slash',
        };
    }

    /**
     * @return array<StatutPaiement>
     */
    public function getNextPossibleStates(): array
    {
        return match ($this) {
            self::EN_ATTENTE => [self::AUTORISE, self::ECHEC, self::ANNULE],
            self::AUTORISE   => [self::CAPTURE, self::ANNULE, self::EXPIRE],
            self::CAPTURE    => [self::REMBOURSE],
            self::ECHEC, self::REMBOURSE, self::ANNULE, self::EXPIRE => [],
        };
    }

    public function getProgressPercentage(): int
    {
        return match ($this) {
            self::EN_ATTENTE => 25,
            self::AUTORISE   => 50,
            self::CAPTURE    => 100,
            self::ECHEC, self::ANNULE, self::EXPIRE => 0,
            self::REMBOURSE => 100, // Techniquement complété mais inversé
        };
    }
}
