<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Application\Contracts\PaginatedResponseInterface;

trait HasPagination
{
    /**
     * Create a LengthAwarePaginator from a PaginatedResponseInterface
     */
    public function createPaginator(
        PaginatedResponseInterface $response,
        Request $request
    ): LengthAwarePaginator {
        $pagination = new LengthAwarePaginator(
            items: $response->getItems(),
            total: $response->getTotal(),
            perPage: $response->getPerPage(),
            currentPage: $response->getPage(),
            options: [
                'path'     => $request->url(),
                'pageName' => 'page',
            ]
        );

        // Ajouter les paramètres de query à la pagination
        $pagination->appends($request->query());

        return $pagination;
    }
}
