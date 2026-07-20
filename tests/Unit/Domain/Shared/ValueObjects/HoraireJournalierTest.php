<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\HoraireJournalier;

describe('HoraireJournalier ValueObject', function () {

    it('can create horaire with times', function () {
        $horaire = HoraireJournalier::create('09:00', '18:00');

        expect($horaire)->toBeInstanceOf(HoraireJournalier::class);
        expect($horaire->ouverture)->toBe('09:00');
        expect($horaire->fermeture)->toBe('18:00');
    });

    it('can check if open', function () {
        $horaire = HoraireJournalier::create('09:00', '18:00');

        expect($horaire->isOpen())->toBeTrue();
    });

    it('can create closed day', function () {
        $horaire = HoraireJournalier::createClosed();

        expect($horaire->isOpen())->toBeFalse();
    });
});
