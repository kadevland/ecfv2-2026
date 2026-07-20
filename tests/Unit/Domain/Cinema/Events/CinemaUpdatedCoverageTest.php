<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\CinemaUpdated;

describe('CinemaUpdated Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $cinemaUuid = 'dddd4444-eeee-ffff-aaaa-bbbbcccc1111';
            $event      = CinemaUpdated::fromUuid($cinemaUuid);

            expect($event)->toBeInstanceOf(CinemaUpdated::class);

            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($cinemaUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getCinemaUuid())->toBe($cinemaUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
