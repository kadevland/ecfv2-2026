<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface pour créer des instances de handlers
 */
interface HandlerProviderInterface
{
    /**
     * Crée une instance du handler
     */
    public function make(string $handlerClass): object;
}
