<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer la liste des cinémas pour un select
 */
final class GetCinemasForSelectQuery implements QueryInterface
{
    public function __construct() {}

    public function isValid(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        return [];
    }
}
