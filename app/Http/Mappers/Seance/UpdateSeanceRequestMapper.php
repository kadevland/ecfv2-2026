<?php

declare(strict_types=1);

namespace App\Http\Mappers\Seance;

use App\Http\Mappers\BaseRequestMapper;
use App\Http\Requests\Admin\Seance\UpdateSeanceRequest;
use App\Application\Seance\Commands\UpdateSeance\UpdateSeanceCommand;

/**
 * Mapper pour convertir UpdateSeanceRequest en UpdateSeanceCommand
 */
final class UpdateSeanceRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateSeanceRequest en UpdateSeanceCommand
     */
    public static function toCommand(UpdateSeanceRequest $request, string $seanceUuid): UpdateSeanceCommand
    {
        $validated = $request->validated();

        // Construire la date/heure de début à partir des champs séparés
        $dateHeureDebut = null;
        if (isset($validated['date_seance']) && isset($validated['heure_debut'])) {
            $dateHeureDebut = $validated['date_seance'] . ' ' . $validated['heure_debut'] . ':00';
        } elseif (isset($validated['date_heure_debut'])) {
            $dateHeureDebut = $validated['date_heure_debut'];
        }

        // Construire la tarification à partir des champs tarifs
        $tarification = null;
        if (isset($validated['tarifs'])) {
            $tarifs       = $validated['tarifs'];
            $tarification = [];

            // Convertir les prix de euros vers centimes (comme dans CreateSeanceRequestMapper)
            foreach (['normal', 'reduit', 'enfant'] as $type) {
                if (isset($tarifs[$type]) && $tarifs[$type] !== '') {
                    $tarification[$type] = (int) round((float) $tarifs[$type] * 100);
                }
            }

            // Si aucun tarif valide, ne pas envoyer la tarification
            if (empty($tarification)) {
                $tarification = null;
            }
        }

        return new UpdateSeanceCommand(
            seanceUuid: $seanceUuid,
            dateHeureDebut: self::sanitizeString($dateHeureDebut),
            dureeAdditionnelle: isset($validated['duree_additionnelle']) ? (int) $validated['duree_additionnelle'] : null,
            filmUuid: self::sanitizeString($validated['film_id'] ?? null),
            salleUuid: self::sanitizeString($validated['salle_id'] ?? null),
            version: self::sanitizeString($validated['version'] ?? null),
            tarification: $tarification,
            placementLibre: isset($validated['placement_libre']) ? (bool) $validated['placement_libre'] : null,
            qualiteProjection: self::sanitizeString($validated['qualite_projection'] ?? null),
            qualiteSonore: self::sanitizeString($validated['qualite_sonore'] ?? null),
            statut: self::sanitizeString($validated['statut'] ?? null),
        );
    }
}
