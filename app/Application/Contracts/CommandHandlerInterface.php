<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Interface pour les Command Handlers
 */
interface CommandHandlerInterface
{
    /**
     * Traite une command et retourne un résultat
     */
    public function handle(CommandInterface $command): Result;
}
