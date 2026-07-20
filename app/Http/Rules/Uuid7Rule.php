<?php

declare(strict_types=1);

namespace App\Http\Rules;

use Closure;
use Exception;
use Symfony\Component\Uid\Uuid;
use Illuminate\Contracts\Validation\ValidationRule;

final class Uuid7Rule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Le :attribute doit être une chaîne de caractères.');

            return;
        }

        try {
            $uuid = Uuid::fromString($value);

            // Vérifier que c'est bien un UUID v7
            if ($uuid->toRfc4122() !== $value || !str_starts_with(substr($value, 14, 1), '7')) {
                $fail('Le :attribute doit être un UUID version 7 valide.');
            }
        } catch (Exception) {
            $fail('Le :attribute doit être un UUID version 7 valide.');
        }
    }
}
