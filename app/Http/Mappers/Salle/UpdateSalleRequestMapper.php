<?php

declare(strict_types=1);

namespace App\Http\Mappers\Salle;

use App\Http\Mappers\BaseRequestMapper;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Http\Requests\Admin\Salle\UpdateSalleRequest;
use App\Application\Salle\Commands\UpdateSalle\UpdateSalleCommand;

/**
 * Mapper pour convertir UpdateSalleRequest en UpdateSalleCommand
 */
final class UpdateSalleRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateSalleRequest en UpdateSalleCommand
     */
    public static function toCommand(UpdateSalleRequest $request, string $uuid): UpdateSalleCommand
    {
        $validated = $request->validated();

        return new UpdateSalleCommand(
            salleUuid: $uuid,
            nom: self::sanitizeString($validated['nom'] ?? null),
            capaciteTotale: isset($validated['capacite_totale']) ? self::toInt($validated['capacite_totale']) : null,
            nombreRangees: isset($validated['nombre_rangees']) ? self::toInt($validated['nombre_rangees']) : null,
            placesParRangee: isset($validated['places_par_rangee']) ? self::toInt($validated['places_par_rangee']) : null,
            placesStandard: isset($validated['places_standard']) ? self::toInt($validated['places_standard']) : null,
            placesPmr: isset($validated['places_pmr']) ? self::toInt($validated['places_pmr']) : null,
            qualiteProjection: self::mapQualiteProjection($validated['qualite_projection'] ?? null),
            qualiteSonore: self::mapQualiteSonore($validated['qualite_sonore'] ?? null),
            accessibilitePmr: isset($validated['accessibilite_pmr']) ? self::toBool($validated['accessibilite_pmr']) : null,
            climatisation: isset($validated['climatisation']) ? self::toBool($validated['climatisation']) : null,
            planSalle: $validated['plan_salle'] ?? null,
            statut: self::mapStatut($validated['statut'] ?? null),
        );
    }

    /**
     * Convertit un array de strings en array d'enums QualiteProjection
     *
     * @param array<string>|null $qualites
     * @return array<QualiteProjection>|null
     */
    private static function mapQualiteProjection(?array $qualites): ?array
    {
        if ($qualites === null || empty($qualites)) {
            return null;
        }

        return array_map(
            fn (string $qualite) => QualiteProjection::from($qualite),
            $qualites
        );
    }

    /**
     * Convertit un array de strings en array d'enums QualiteSonore
     *
     * @param array<string>|null $qualites
     * @return array<QualiteSonore>|null
     */
    private static function mapQualiteSonore(?array $qualites): ?array
    {
        if ($qualites === null || empty($qualites)) {
            return null;
        }

        return array_map(
            fn (string $qualite) => QualiteSonore::from($qualite),
            $qualites
        );
    }

    /**
     * Convertit un string en enum StatutSalle
     */
    private static function mapStatut(?string $statut): ?StatutSalle
    {
        return $statut ? StatutSalle::from($statut) : null;
    }
}
