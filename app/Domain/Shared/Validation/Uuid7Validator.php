<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validation;

use Exception;
use Symfony\Component\Uid\Uuid;
use Respect\Validation\Rules\AbstractRule;

/**
 * Validateur UUID7 pour Respect Validation
 */
final class Uuid7Validator extends AbstractRule
{
    public function validate($input): bool
    {
        if (!is_string($input)) {
            return false;
        }

        try {
            $uuid = Uuid::fromString($input);

            // Vérifier que c'est bien un UUID v7
            return $uuid->toRfc4122() === $input && str_starts_with(substr($input, 14, 1), '7');
        } catch (Exception) {
            return false;
        }
    }
}
