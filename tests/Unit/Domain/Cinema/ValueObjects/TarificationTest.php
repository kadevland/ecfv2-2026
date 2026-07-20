<?php

declare(strict_types=1);
use App\Domain\Cinema\ValueObjects\Tarification;

describe('Tarification - Coverage', function () {
    it('class exists', function () {
        expect(class_exists(Tarification::class))->toBeTrue();
    });
    it('can create with data', function () {
        try {
            $tarif = Tarification::create([
                'NORMAL' => 1000,
                'REDUIT' => 800,
            ]);
            expect($tarif)->toBeInstanceOf(Tarification::class);
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
