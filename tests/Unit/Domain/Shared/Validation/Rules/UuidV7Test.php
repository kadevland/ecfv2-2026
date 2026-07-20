<?php

declare(strict_types=1);
use App\Domain\Shared\Validation\Rules\UuidV7;

describe('UuidV7 Rule', function () {
    it('exists', function () {
        expect(class_exists(UuidV7::class))->toBeTrue();
    });
    it('can create', function () {
        $rule = new UuidV7;
        expect($rule)->toBeInstanceOf(UuidV7::class);
    });
});
