<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\SalleCreated;
use App\Domain\Cinema\ValueObjects\SalleId;

describe('SalleCreated Event', function () {

    it('can be created with salle ID', function () {
        $salleId = SalleId::generate();
        $event   = new SalleCreated($salleId);

        expect($event)->toBeInstanceOf(SalleCreated::class);
        expect($event->salleId)->toBe($salleId);
    });

    it('has occurred at timestamp', function () {
        $salleId = SalleId::generate();
        $event   = new SalleCreated($salleId);

        expect($event->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
    });
});
