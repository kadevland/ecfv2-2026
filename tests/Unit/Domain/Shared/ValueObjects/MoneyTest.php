<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Money;

describe('Money ValueObject', function () {

    it('can create money with amount', function () {
        $money = Money::fromAmount(1000, 'EUR');

        expect($money)->toBeInstanceOf(Money::class);
        expect($money->getAmount())->toBe(1000);
        expect($money->getCurrency())->toBe('EUR');
    });

    it('can format money', function () {
        $money     = Money::fromAmount(1250, 'EUR');
        $formatted = $money->format();

        expect($formatted)->toBeString();
        expect($formatted)->toContain('12');
        expect($formatted)->toContain('50');
    });
});
