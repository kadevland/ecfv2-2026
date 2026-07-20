<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validation\Rules;

use Exception;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Rules\Core\Simple;

/**
 * Custom Respect/Validation rule for Ramsey UUID validation
 */
class UuidV7 extends Simple
{
    /**
     * Valide qu'une valeur est un UUID v7
     */
    public function isValid(mixed $input): bool
    {
        // Vérifier que c'est une chaîne
        if (!is_string($input)) {
            return false;
        }

        // Vérifier que c'est un UUID valide
        if (!Uuid::isValid($input)) {
            return false;
        }

        try {
            $uuid = Uuid::fromString($input);

            // Check version through string parsing since getVersion() may not be available
            $uuidString = $uuid->toString();
            $version    = hexdec(substr($uuidString, 14, 1));

            return $version === 7;

        } catch (Exception $e) {
            return false;
        }
    }
}
