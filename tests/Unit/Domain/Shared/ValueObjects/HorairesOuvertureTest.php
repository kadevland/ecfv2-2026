<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\HoraireJournalier;
use App\Domain\Shared\ValueObjects\HorairesOuverture;

describe('HorairesOuverture ValueObject', function () {

    it('can create horaires with days', function () {
        $lundi = HoraireJournalier::create('09:00', '18:00');
        $mardi = HoraireJournalier::create('09:00', '18:00');

        $horaires = HorairesOuverture::create([
            'lundi' => $lundi,
            'mardi' => $mardi,
        ]);

        expect($horaires)->toBeInstanceOf(HorairesOuverture::class);
    });

    it('can check if open on day', function () {
        $lundi = HoraireJournalier::create('09:00', '18:00');

        $horaires = HorairesOuverture::create([
            'lundi' => $lundi,
        ]);

        expect($horaires->isOpenOn('lundi'))->toBeTrue();
    });
});
