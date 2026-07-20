<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\PaginationCriteria;

describe('PaginationCriteria ValueObject', function () {

    it('can create pagination criteria', function () {
        $criteria = PaginationCriteria::create(1, 10);

        expect($criteria)->toBeInstanceOf(PaginationCriteria::class);
        expect($criteria->page)->toBe(1);
        expect($criteria->perPage)->toBe(10);
    });

    it('can calculate offset', function () {
        $criteria = PaginationCriteria::create(2, 10);

        expect($criteria->getOffset())->toBe(10);
    });

    it('has default values', function () {
        $criteria = PaginationCriteria::default();

        expect($criteria->page)->toBeInt();
        expect($criteria->perPage)->toBeInt();
    });
});
