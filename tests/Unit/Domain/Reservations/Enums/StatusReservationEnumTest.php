<?php

declare(strict_types=1);
use App\Domain\Reservations\Enums\StatusReservationEnum;

describe('StatusReservationEnum', function () {
    it('complete coverage', function () {
        foreach (StatusReservationEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect(StatusReservationEnum::from($case->value))->toBe($case);
            expect(StatusReservationEnum::tryFrom($case->value))->toBe($case);
        }
        expect(StatusReservationEnum::tryFrom('invalid'))->toBeNull();
    });
});
