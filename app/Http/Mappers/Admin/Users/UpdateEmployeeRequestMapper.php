<?php

declare(strict_types=1);

namespace App\Http\Mappers\Admin\Users;

use App\Http\Mappers\BaseRequestMapper;
use App\Http\Requests\Admin\Users\UpdateEmployeeRequest;
use App\Application\Users\Commands\UpdateEmployee\UpdateEmployeeCommand;

/**
 * Mapper pour convertir UpdateEmployeeRequest en UpdateEmployeeCommand
 */
final class UpdateEmployeeRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateEmployeeRequest en UpdateEmployeeCommand
     */
    public static function toCommand(UpdateEmployeeRequest $request, string $uuid): UpdateEmployeeCommand
    {
        $validated = $request->validated();

        return new UpdateEmployeeCommand(
            userUuid: $uuid,
            prenom: self::sanitizeString($validated['prenom'] ?? null),
            nom: self::sanitizeString($validated['nom'] ?? null),
            email: self::validateEmail($validated['email'] ?? null),
            telephone: self::sanitizePhone($validated['telephone'] ?? null),
            dateNaissance: self::sanitizeString($validated['date_naissance'] ?? null),
            sexe: self::sanitizeString($validated['sexe'] ?? null),
            adresse: self::buildAdresseArray($validated),
            isActive: isset($validated['est_actif']) ? self::toBool($validated['est_actif']) : null,
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
