<?php

declare(strict_types=1);

namespace App\Http\Mappers\Film;

use App\Http\Mappers\BaseRequestMapper;
use App\Application\Film\Commands\CreateFilm\CreateFilmCommand;

/**
 * Mapper pour convertir les données de requête HTTP en CreateFilmCommand
 */
final class CreateFilmRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit les données de requête HTTP en Command
     *
     * @param array<string, mixed> $data
     */
    public static function toCommand(array $data): CreateFilmCommand
    {
        // Convertir le genre string en array de genres
        $genres = [];
        if (isset($data['genre']) && !empty($data['genre'])) {
            $genres = self::sanitizeArrayString($data['genre']);
        }

        // Traiter les acteurs principaux (peut être un string multilignes)
        $acteursPrincipaux = [];
        if (isset($data['acteurs_principaux']) && !empty($data['acteurs_principaux'])) {
            if (is_string($data['acteurs_principaux'])) {
                // Split par lignes et nettoyer
                $acteursPrincipaux = array_filter(
                    array_map('trim', explode("\n", $data['acteurs_principaux'])),
                    fn ($actor) => !empty($actor)
                );
            } elseif (is_array($data['acteurs_principaux'])) {
                $acteursPrincipaux = self::sanitizeArray($data['acteurs_principaux']);
            }
        }

        // Traiter les réalisateurs (string → array)
        $realisateurs = [];
        if (isset($data['realisateurs']) && !empty($data['realisateurs'])) {
            if (is_string($data['realisateurs'])) {
                $realisateurs = array_filter(
                    array_map('trim', explode("\n", $data['realisateurs'])),
                    fn ($item) => !empty($item)
                );
            } elseif (is_array($data['realisateurs'])) {
                $realisateurs = self::sanitizeArray($data['realisateurs']);
            }
        }

        return new CreateFilmCommand(
            titre: self::sanitizeString($data['titre']) ?? '',
            realisateurs: $realisateurs,
            genres: $genres,
            dureeMinutes: self::toInt($data['duree_minutes'] ?? 0),
            classification: self::sanitizeString($data['classification']) ?? '',
            dateSortie: self::sanitizeString($data['date_sortie']) ?? '',
            titreFr: self::sanitizeString($data['titre_original'] ?? null),
            acteursPrincipaux: $acteursPrincipaux,
            langueOriginale: self::sanitizeString($data['langue_originale'] ?? null),
            sousTitres: self::sanitizeString($data['sous_titres'] ?? null),
            resume: self::sanitizeString($data['synopsis'] ?? null),
            dateFinExploitation: self::sanitizeString($data['date_fin_exploitation'] ?? null),
            notePresse: isset($data['note_critique']) && !empty($data['note_critique']) ? (float) $data['note_critique'] : null,
            notePublic: isset($data['note_public']) && !empty($data['note_public']) ? (float) $data['note_public'] : null,
            afficheUrl: self::sanitizeString($data['affiche_url'] ?? null),
            bandeAnnonceUrl: self::sanitizeString($data['bande_annonce_url'] ?? null),
            estActif: self::toBool($data['est_actif'] ?? true),
        );
    }

    /**
     * Nettoie et valide un tableau de chaînes
     *
     * @param array<mixed> $array
     * @return array<string>
     */
    private static function sanitizeArray(array $array): array
    {
        return array_filter(
            array_map(
                fn ($item) => self::sanitizeString((string) $item),
                $array
            ),
            fn ($item) => $item !== null && $item !== ''
        );
    }
}
