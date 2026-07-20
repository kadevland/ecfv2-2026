<?php

declare(strict_types=1);

namespace App\Http\Mappers\Incident;

use Illuminate\Http\Request;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\SeveriteIncident;
use App\Application\Employees\Commands\DeclareIncident\DeclareIncidentCommand;

final class DeclareIncidentRequestMapper
{
    /**
     * Convertit une DeclareIncidentRequest validée en DeclareIncidentCommand
     */
    public static function toCommand(Request $request): DeclareIncidentCommand
    {
        // Gérer les pièces jointes si présentes
        $piecesJointes = null;
        if ($request->hasFile('pieces_jointes')) {
            $piecesJointes = [];
            foreach ($request->file('pieces_jointes') as $file) {

                $path            = $file->store('incidents', 'public');
                $piecesJointes[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path'     => $path,
                    'type'     => $file->getMimeType(),
                ];
            }
        }

        // Pour l'instant on utilise un UUID fixe pour tester
        $emploiDeclarantUuid = '01940725-9d48-7a82-b5e3-cb9f12345678';

        // Pour l'instant on utilise un UUID fixe pour tester
        $cinemaUuid = '01940725-9d48-7a82-b5e3-cb9f12345678';

        return new DeclareIncidentCommand(
            emploiDeclarantUuid: $emploiDeclarantUuid,
            cinemaUuid: $cinemaUuid,
            typeIncident: TypeIncident::from($request->input('type_incident')),
            severite: SeveriteIncident::from($request->input('severite')),
            titre: $request->input('titre'),
            description: $request->input('description'),
            salleUuid: $request->input('salle_uuid'),
            piecesJointes: $piecesJointes
        );
    }
}
