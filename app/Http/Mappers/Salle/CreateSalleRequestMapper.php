<?php

declare(strict_types=1);

namespace App\Http\Mappers\Salle;

use App\Http\Mappers\BaseRequestMapper;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Http\Requests\Admin\Salle\CreateSalleRequest;
use App\Application\Salle\Commands\CreateSalle\CreateSalleCommand;

/**
 * Mapper pour convertir CreateSalleRequest en CreateSalleCommand
 */
final class CreateSalleRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une CreateSalleRequest en CreateSalleCommand
     */
    public static function toCommand(CreateSalleRequest $request): CreateSalleCommand
    {
        $validated = $request->validated();

        return new CreateSalleCommand(
            cinemaUuid: self::sanitizeString($validated['cinema_uuid']),
            nom: self::sanitizeString($validated['nom']),
            capaciteTotale: (int) $validated['capacite_totale'],
            nombreRangees: (int) $validated['nombre_rangees'],
            placesParRangee: (int) $validated['places_par_rangee'],
            placesStandard: (int) $validated['places_standard'],
            placesPmr: (int) $validated['places_pmr'],
            qualiteProjection: self::mapQualiteProjection($validated['qualite_projection'] ?? []),
            qualiteSonore: self::mapQualiteSonore($validated['qualite_sonore'] ?? []),
            climatisation: self::toBool($validated['climatisation'] ?? true),
            accessibilitePmr: self::toBool($validated['accessibilite_pmr'] ?? true),
            planSalle: $validated['plan_salle'] ?? null,
            statut: StatutSalle::from($validated['statut'] ?? 'ACTIVE'),
        );
    }

    /**
     * Convertit un array de strings en array d'enums QualiteProjection
     *
     * @param array<string> $qualites
     * @return array<QualiteProjection>
     */
    private static function mapQualiteProjection(array $qualites): array
    {
        if (empty($qualites)) {
            return [];
        }

        return array_map(
            fn (string $qualite) => QualiteProjection::from($qualite),
            $qualites
        );
    }

    /**
     * Convertit un array de strings en array d'enums QualiteSonore
     *
     * @param array<string> $qualites
     * @return array<QualiteSonore>
     */
    private static function mapQualiteSonore(array $qualites): array
    {
        if (empty($qualites)) {
            return [];
        }

        return array_map(
            fn (string $qualite) => QualiteSonore::from($qualite),
            $qualites
        );
    }
}
