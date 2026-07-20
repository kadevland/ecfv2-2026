<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\CinemaCreated;
use App\Domain\Cinema\ValueObjects\CinemaId;

describe('CinemaCreated Event', function () {

    it('can be created with cinema ID', function () {
        $cinemaId = CinemaId::generate();
        $event    = new CinemaCreated($cinemaId);

        expect($event)->toBeInstanceOf(CinemaCreated::class);
        expect($event->cinemaId)->toBe($cinemaId);
    });

    it('has occurred at timestamp', function () {
        $cinemaId = CinemaId::generate();
        $event    = new CinemaCreated($cinemaId);

        expect($event->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
    });
});
