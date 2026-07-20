<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validation;

use InvalidArgumentException;
use App\Domain\Shared\Validation\Rules\UuidV7;

final class UuidV7Validator
{
    /**
     * Validate that a string is a valid UUID v7
     */
    public static function validate(string $uuid, string $fieldName = 'UUID'): void
    {
        if (empty($uuid)) {
            throw new InvalidArgumentException("{$fieldName} cannot be empty");
        }

        $rule = new UuidV7;
        if (!$rule->isValid($uuid)) {
            throw new InvalidArgumentException("{$fieldName} must be a valid UUID v7");
        }
    }

    /**
     * Check if a string is a valid UUID v7
     */
    public static function isValid(string $uuid): bool
    {
        if (empty($uuid)) {
            return false;
        }

        $rule = new UuidV7;

        return $rule->isValid($uuid);
    }

    /**
     * Validate optional UUID v7 (can be null or empty)
     */
    public static function validateOptional(?string $uuid, string $fieldName = 'UUID'): void
    {
        if ($uuid === null || $uuid === '') {
            return;
        }

        self::validate($uuid, $fieldName);
    }

    /**
     * Validate multiple UUID v7s
     *
     * @param array<mixed> $uuids
     */
    public static function validateMultiple(array $uuids, string $fieldName = 'UUIDs'): void
    {
        if (empty($uuids)) {
            throw new InvalidArgumentException("{$fieldName} cannot be empty");
        }

        foreach ($uuids as $index => $uuid) {
            if (!is_string($uuid)) {
                throw new InvalidArgumentException("{$fieldName}[{$index}] must be a string");
            }

            self::validate($uuid, "{$fieldName}[{$index}]");
        }
    }
}
