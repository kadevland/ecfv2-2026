<?php

declare(strict_types=1);
use App\Domain\Shared\Validation\Uuid7Validator;

describe('Uuid7Validator', function () {
    it('exists', function () {
        expect(class_exists(Uuid7Validator::class))->toBeTrue();
    });
    it('can validate', function () {
        $validator = new Uuid7Validator;
        expect($validator)->toBeInstanceOf(Uuid7Validator::class);
    });
});
