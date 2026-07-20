<?php

declare(strict_types=1);

namespace App\Http\Mappers\Employees;

use Illuminate\Http\Request;
use App\Application\Employees\Commands\UpdateEmployeeJob\UpdateEmployeeJobCommand;

final class UpdateEmployeeJobRequestMapper
{
    public static function toCommand(Request $request, string $userUuid): UpdateEmployeeJobCommand
    {
        return new UpdateEmployeeJobCommand(
            userUuid: $userUuid,
            titrePoste: $request->input('titre_poste'),
            description: $request->input('description'),
            categorie: $request->input('categorie'),
            niveau: $request->input('niveau'),
            typeContrat: $request->input('type_contrat'),
            tempsTravail: $request->input('temps_travail'),
            cinemaId: $request->input('cinema_id'),
            salaireMensuel: $request->input('salaire_mensuel') ? (float) $request->input('salaire_mensuel') : null,
            dateEmbauche: $request->input('date_embauche'),
            encadrementEquipe: $request->boolean('encadrement_equipe'),
            nombrePersonnesEncadrees: $request->input('nombre_personnes_encadrees') ? (int) $request->input('nombre_personnes_encadrees') : null,
            travailWeekend: $request->boolean('travail_weekend'),
            travailFeries: $request->boolean('travail_feries'),
            travailSoiree: $request->boolean('travail_soiree'),
        );
    }
}
