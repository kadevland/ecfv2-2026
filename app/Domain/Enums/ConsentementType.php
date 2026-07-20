<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ConsentementType: string
{
    case NEWSLETTER     = 'newsletter';
    case ALERTES_CINEMA = 'alertes_cinema';
    case PROMOTIONS     = 'promotions';
    case ANALYTICS      = 'analytics';

    public function label(): string
    {
        return match ($this) {
            self::NEWSLETTER     => 'Newsletter',
            self::ALERTES_CINEMA => 'Alertes cinéma',
            self::PROMOTIONS     => 'Promotions',
            self::ANALYTICS      => 'Analytics',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NEWSLETTER     => 'Recevoir la newsletter avec les actualités cinéma',
            self::ALERTES_CINEMA => 'Recevoir des alertes sur les nouveaux films et séances',
            self::PROMOTIONS     => 'Recevoir les offres promotionnelles et réductions',
            self::ANALYTICS      => 'Autoriser l\'analyse de votre utilisation pour améliorer nos services',
        };
    }

    public function isMarketing(): bool
    {
        return in_array($this, [
            self::NEWSLETTER,
            self::ALERTES_CINEMA,
            self::PROMOTIONS,
        ]);
    }
}
