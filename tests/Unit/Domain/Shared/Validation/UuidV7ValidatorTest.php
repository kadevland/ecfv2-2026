<?php

declare(strict_types=1);
use App\Domain\Shared\Validation\UuidV7Validator;

describe('UuidV7Validator', function () {
    it('exists', function () {
        expect(class_exists(UuidV7Validator::class))->toBeTrue();
    });
    it('can create', function () {
        $validator = new UuidV7Validator;
        expect($validator)->toBeInstanceOf(UuidV7Validator::class);
    });
});
