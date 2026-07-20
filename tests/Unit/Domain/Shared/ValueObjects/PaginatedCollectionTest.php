<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\PaginatedCollection;

describe('PaginatedCollection ValueObject', function () {

    it('can create paginated collection', function () {
        $items      = ['item1', 'item2', 'item3'];
        $collection = PaginatedCollection::create($items, 100, 1, 10);

        expect($collection)->toBeInstanceOf(PaginatedCollection::class);
        expect($collection->items)->toBe($items);
        expect($collection->total)->toBe(100);
        expect($collection->page)->toBe(1);
        expect($collection->perPage)->toBe(10);
    });

    it('can check if has more pages', function () {
        $collection = PaginatedCollection::create([], 100, 1, 10);

        expect($collection->hasMorePages())->toBeTrue();
    });

    it('can get total pages', function () {
        $collection = PaginatedCollection::create([], 100, 1, 10);

        expect($collection->totalPages())->toBe(10);
    });
});
