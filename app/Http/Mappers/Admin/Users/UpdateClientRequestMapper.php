<?php

declare(strict_types=1);

namespace App\Http\Mappers\Admin\Users;

use App\Http\Mappers\BaseRequestMapper;
use App\Http\Requests\Admin\Users\UpdateClientRequest;
use App\Application\Users\Commands\UpdateClient\UpdateClientCommand;

/**
 * Mapper pour convertir UpdateClientRequest en UpdateClientCommand
 */
final class UpdateClientRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateClientRequest en UpdateClientCommand
     */
    public static function toCommand(UpdateClientRequest $request, string $uuid): UpdateClientCommand
    {
        $validated = $request->validated();

        return new UpdateClientCommand(
            userUuid: $uuid,
            prenom: self::sanitizeString($validated['prenom'] ?? null),
            nom: self::sanitizeString($validated['nom'] ?? null),
            email: self::validateEmail($validated['email'] ?? null),
            telephone: self::sanitizePhone($validated['telephone'] ?? null),
            dateNaissance: self::sanitizeString($validated['date_naissance'] ?? null),
            sexe: self::sanitizeString($validated['sexe'] ?? null),
            adresse: self::buildAdresseArray($validated),
            isActive: isset($validated['is_active']) ? self::toBool($validated['is_active']) : null,
        );
    }

    /**
     * Reconstruit l'array adresse à partir des champs séparés du form
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>|null
     */
    private static function buildAdresseArray(array $validated): ?array
    {
        // Si aucun champ adresse n'est présent, retourner null
        if (empty($validated['adresse']) && empty($validated['ville']) &&
            empty($validated['code_postal']) && empty($validated['pays'])) {
            return null;
        }

        return [
            'rue'         => self::sanitizeString($validated['adresse'] ?? null),
            'ville'       => self::sanitizeString($validated['ville'] ?? null),
            'code_postal' => self::sanitizeString($validated['code_postal'] ?? null),
            'pays'        => self::sanitizeString($validated['pays'] ?? null),
        ];
    }
}
