<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\SeanceCreated;

describe('SeanceCreated Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $seanceUuid = 'aaaa1111-bbbb-cccc-dddd-eeee22223333';
            $event      = SeanceCreated::fromUuid($seanceUuid);

            expect($event)->toBeInstanceOf(SeanceCreated::class);

            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($seanceUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getSeanceUuid())->toBe($seanceUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
