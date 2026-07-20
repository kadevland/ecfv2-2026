<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface pour les Query Handlers
 */
interface QueryHandlerInterface
{
    /**
     * Traite une query et retourne un résultat
     */
    public function handle(QueryInterface $query): Result;
}
