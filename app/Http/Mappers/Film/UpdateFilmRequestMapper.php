<?php

declare(strict_types=1);

namespace App\Http\Mappers\Film;

use App\Http\Mappers\BaseRequestMapper;
use App\Http\Requests\Admin\Film\UpdateFilmRequest;
use App\Application\Film\Commands\UpdateFilm\UpdateFilmCommand;

/**
 * Mapper pour convertir UpdateFilmRequest en UpdateFilmCommand
 */
final class UpdateFilmRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une UpdateFilmRequest en UpdateFilmCommand
     */
    public static function toCommand (UpdateFilmRequest $request, string $filmUuid) : UpdateFilmCommand
    {


        $validated = $request->validated();

        return new UpdateFilmCommand(
            filmUuid: $filmUuid,
            titre: self::sanitizeString($validated['titre'] ?? null),
            titreOriginal: self::sanitizeString($validated['titre_original'] ?? null),
            synopsis: self::sanitizeString($validated['synopsis'] ?? null),
            genre: self::sanitizeArrayString($validated['genre'] ?? null),
            realisateurs: $validated['realisateurs'] ?? null,
            acteursPrincipaux: $validated['acteurs_principaux'] ?? null,//self::sanitizeString($validated['acteurs_principaux'] ?? null),
            dureeMinutes: isset($validated['duree_minutes']) ? self::toInt($validated['duree_minutes']) : null,
            dateSortie: self::sanitizeString($validated['date_sortie'] ?? null),
            paysOrigine: self::sanitizeString($validated['pays_origine'] ?? null),
            langueOriginale: self::sanitizeString($validated['langue_originale'] ?? null),
            classification: self::sanitizeString($validated['classification'] ?? null),
            producteur: self::sanitizeString($validated['producteur'] ?? null),
            afficheUrl: self::sanitizeString($validated['affiche_url'] ?? null),
            bandeAnnonceUrl: self::sanitizeString($validated['bande_annonce_url'] ?? null),
            imagesAdditionnelles: $validated['images_additionnelles'] ?? null,
            noteCritique: isset($validated['note_critique']) ? (float) $validated['note_critique'] : null,
            notePublic: isset($validated['note_public']) ? (float) $validated['note_public'] : null,
            statut: self::sanitizeString($validated['statut'] ?? null),
            estActif: isset($validated['est_actif']) ? self::toBool($validated['est_actif']) : null,
            metadonneesTechniques: $validated['metadonnees_techniques'] ?? null,
        );
    }
}
