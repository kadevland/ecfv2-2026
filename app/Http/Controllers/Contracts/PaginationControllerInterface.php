<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Application\Contracts\PaginatedResponseInterface;

interface PaginationControllerInterface
{
    /**
     * Create a LengthAwarePaginator from a PaginatedResponseInterface
     *
     * @return LengthAwarePaginator<int, mixed>
     */
    public function createPaginator(
        PaginatedResponseInterface $response,
        Request $request
    ): LengthAwarePaginator;
}
