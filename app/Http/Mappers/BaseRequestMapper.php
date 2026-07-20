<?php

declare(strict_types=1);

namespace App\Http\Mappers;

/**
 * Base abstract class pour les Request Mappers
 *
 * Fournit des méthodes communes pour la conversion de données HTTP
 */
abstract class BaseRequestMapper
{
    /**
     * Nettoie et valide une chaîne de caractères
     */
    protected static function sanitizeString(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    /**
     * Convertit une valeur en booléen avec gestion des cas edge
     */
    protected static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Convertit une valeur en entier avec valeur par défaut
     */
    protected static function toInt(mixed $value, int $default = 0): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    /**
     * Valide et formate un email
     */
    protected static function validateEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = trim(strtolower($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Nettoie un numéro de téléphone
     */
    protected static function sanitizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        // Supprime tous les caractères non numériques et espaces/tirets
        $phone = preg_replace('/[^\d\s\-\+\(\)]/u', '', $phone);

        return trim($phone) ?: null;
    }

    protected static function sanitizeArrayString(?array $array): ?array
    {
        if ($array === null) {
            return null;
        }

        $sanitizedArray = array_map(function ($item) {
            if (is_string($item)) {
                return trim($item);
            }
            return $item;
        }, $array);

        // Filtre les éléments vides après le trim
        $sanitizedArray = array_filter($sanitizedArray, function ($item) {
            return !empty($item);
        });

        return !empty($sanitizedArray) ? array_values($sanitizedArray) : null;

    }
}
