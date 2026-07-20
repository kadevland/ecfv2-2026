<?php

declare(strict_types=1);

namespace App\Http\Mappers\Cinema;

use App\Http\Mappers\BaseRequestMapper;
use App\Http\Requests\Admin\Cinema\UpdateCinemaRequest;
use App\Application\Cinema\Commands\UpdateCinema\UpdateCinemaCommand;

// use App\Domain\Shared\ValueObjects\HorairesOuverture;

/**
 * Mapper pour convertir UpdateCinemaRequest en UpdateCinemaCommand
 */
final class UpdateCinemaRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateCinemaRequest en UpdateCinemaCommand
     */
    public static function toCommand(UpdateCinemaRequest $request, string $cinemaUuid): UpdateCinemaCommand
    {
        $validated = $request->validated();

        // Traiter les horaires si présents - garder l'array simple
        $horaires = null;
        if (isset($validated['horaires']) && is_array($validated['horaires'])) {
            $horaires = $validated['horaires'];
        }

        return new UpdateCinemaCommand(
            cinemaUuid: $cinemaUuid,
            nom: self::sanitizeString($validated['nom'] ?? null),
            pays: self::sanitizeString($validated['pays'] ?? null),
            rue: self::sanitizeString($validated['rue'] ?? null),
            ville: self::sanitizeString($validated['ville'] ?? null),
            codePostal: self::sanitizeString($validated['code_postal'] ?? null),
            telephone: self::sanitizePhone($validated['telephone'] ?? null),
            email: self::validateEmail($validated['email'] ?? null),
            description: self::sanitizeString($validated['description'] ?? null),
            estActif: isset($validated['est_actif']) ? self::toBool($validated['est_actif']) : null,
            latitude: isset($validated['latitude']) && $validated['latitude'] !== ''
                ? (float) $validated['latitude']
                : null,
            longitude: isset($validated['longitude']) && $validated['longitude'] !== ''
                ? (float) $validated['longitude']
                : null,
            horaires: $horaires,
        );
    }
}
