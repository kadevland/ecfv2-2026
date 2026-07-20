<?php

declare(strict_types=1);

use Carbon\Carbon;
use App\Domain\Shared\ValueObjects\DateNaissance;

describe('DateNaissance ValueObject', function () {

    it('accepte une date de naissance valide', function () {
        $date          = Carbon::create(1990, 6, 15);
        $dateNaissance = DateNaissance::fromCarbon($date);

        expect($dateNaissance->value->format('Y-m-d'))->toBe('1990-06-15');
    });

    it('calcule l\'�ge correctement', function () {
        $date          = Carbon::now()->subYears(25);
        $dateNaissance = DateNaissance::fromCarbon($date);

        expect($dateNaissance->getAge())->toBe(25);
    });

    it('rejette une date future', function () {
        $dateFuture = Carbon::tomorrow();

        expect(fn () => DateNaissance::fromCarbon($dateFuture))
            ->toThrow(InvalidArgumentException::class);
    });

    it('v�rifie si majeur', function () {
        $dateAdulte    = Carbon::now()->subYears(25);
        $dateNaissance = DateNaissance::fromCarbon($dateAdulte);

        expect($dateNaissance->isMajeur())->toBeTrue();
    });

    it('v�rifie si mineur', function () {
        $dateMineur    = Carbon::now()->subYears(15);
        $dateNaissance = DateNaissance::fromCarbon($dateMineur);

        expect($dateNaissance->isMajeur())->toBeFalse();
    });

    it('compare deux dates de naissance', function () {
        $date  = Carbon::create(1990, 6, 15);
        $date1 = DateNaissance::fromCarbon($date);
        $date2 = DateNaissance::fromCarbon($date);

        expect($date1->equals($date2))->toBeTrue();
    });
});
