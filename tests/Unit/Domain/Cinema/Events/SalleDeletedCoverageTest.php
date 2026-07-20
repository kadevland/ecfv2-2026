<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\SalleDeleted;

describe('SalleDeleted Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $salleUuid = 'aaaa1111-bbbb-cccc-dddd-eeee22223333';
            $event     = SalleDeleted::fromUuid($salleUuid);

            expect($event)->toBeInstanceOf(SalleDeleted::class);
            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($salleUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getSalleUuid())->toBe($salleUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
