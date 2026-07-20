<?php

declare(strict_types=1);
use App\Domain\Shared\ValueObjects\MoneyHelper;

describe('MoneyHelper', function () {
    it('exists', function () {
        expect(class_exists(MoneyHelper::class))->toBeTrue();
    });
    it('can call methods', function () {
        $helper = new MoneyHelper;
        expect($helper)->toBeInstanceOf(MoneyHelper::class);
    });
});
