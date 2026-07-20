<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Dashboard employé
 * FAT CONTROLLER : Récupère toutes les données nécessaires pour le dashboard
 */
class DashboardController extends Controller
{
    /**
     * Affiche le dashboard employé
     */
    public function __invoke(Request $request): View
    {
        $user   = auth()->user();
        $emploi = $user->employeeProfile ?? null;
        $profil = $user->profil ?? null;


        // Nom de l'employé
        $employee = [
            'nom'    => $emploi->nom ?? 'Employé',
            'prenom' => $emploi->prenom ?? 'Non défini',
            'poste'  => $emploi->poste ?? 'Employé',
            'cinema' => $emploi->cinema->nom ?? null,
        ];

        // Compteurs du jour
        $nbSeancesDuJour = \App\Infrastructure\Database\Models\Cinema\Seance::on('pgsql')
            ->whereDate('date_heure_debut', now()->toDateString())
            ->count();

        $nbReservationsDuJour = \App\Infrastructure\Database\Models\Reservations\Reservation::on('pgsql')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $nbFilmsDuJour = \App\Infrastructure\Database\Models\Cinema\Seance::on('pgsql')
            ->whereDate('date_heure_debut', now()->toDateString())
            ->distinct('film_uuid')
            ->count('film_uuid');

        // 10 prochaines séances (date_debut > now() - 30min)
        $prochainesSeances = \App\Infrastructure\Database\Models\Cinema\Seance::with(['film', 'salle'])
            ->where('date_heure_debut', '>', now()->subMinutes(30))
            ->orderBy('date_heure_debut')
            ->limit(10)
            ->get();

        return view('employee.dashboard', [
            'employee'             => $employee,
            'nbSeancesDuJour'      => $nbSeancesDuJour,
            'nbReservationsDuJour' => $nbReservationsDuJour,
            'nbFilmsDuJour'        => $nbFilmsDuJour,
            'prochainesSeances'    => $prochainesSeances,
        ]);
    }
}
