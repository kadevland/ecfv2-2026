<?php

declare(strict_types=1);

namespace App\Http\Mappers\Seance;

use Illuminate\Http\Request;
use App\Domain\Enums\StatutSeance;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Seance\Commands\CreateSeance\CreateSeanceCommand;

/**
 * Mapper pour convertir Request en CreateSeanceCommand
 */
final class CreateSeanceRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une CreateSeanceRequest validée en CreateSeanceCommand
     */
    public static function toCommand(Request $request): CreateSeanceCommand
    {
        // Construire le tableau tarification à partir des tarifs du formulaire
        $tarifs     = $request->input('tarifs', []);
        $tarifsBase = [
            'normal' => isset($tarifs['normal']) ? (float) $tarifs['normal'] : 0.0,
            'reduit' => isset($tarifs['reduit']) ? (float) $tarifs['reduit'] : 0.0,
            'enfant' => isset($tarifs['enfant']) ? (float) $tarifs['enfant'] : 0.0,
        ];

        // Construire les options supplémentaires avec les qualités
        $optionsSupplementaires = [];

        if ($qualiteProjection = $request->string('qualite_projection')->toString()) {
            $optionsSupplementaires['qualite_projection'] = $qualiteProjection;
        }

        if ($qualiteSonore = $request->string('qualite_sonore')->toString()) {
            $optionsSupplementaires['qualite_sonore'] = $qualiteSonore;
        }

        // Ajouter la durée additionnelle pour pouvoir la récupérer plus tard
        $dureeAdditionnelle = $request->integer('duree_additionnelle');
        if ($dureeAdditionnelle) {
            $optionsSupplementaires['duree_additionnelle'] = $dureeAdditionnelle;
        }

        // Récupérer la date/heure de début calculée par AlpineJS
        $dateHeureDebut          = $request->string('date_heure_debut')->toString();
        $dateHeureDebutFormatted = \Carbon\Carbon::parse($dateHeureDebut)->format('Y-m-d H:i:s');

        // La durée additionnelle est déjà récupérée plus haut

        return new CreateSeanceCommand(
            filmUuid: $request->string('film_id')->toString(),
            salleUuid: $request->string('salle_id')->toString(),
            dateHeureDebut: $dateHeureDebutFormatted,
            dateHeureFin: null, // Le handler calculera la fin
            version: strtolower($request->string('version')->toString()),
            tarifsBase: $tarifsBase,
            tauxTva: 20.0, // 20% TVA française
            dureeAdditionnelle: $dureeAdditionnelle,
            devise: 'EUR',
            placementLibre: $request->boolean('placement_libre', false),
            statut: $request->string('statut', StatutSeance::PROGRAMMEE->value)->toString(),
            optionsSupplementaires: !empty($optionsSupplementaires) ? $optionsSupplementaires : null,
        );
    }
}
