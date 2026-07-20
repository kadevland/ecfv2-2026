<?php

declare(strict_types=1);

namespace Database\Seeders\Cinema;

use Illuminate\Database\Seeder;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Models\Cinema\Cinema;

final class SalleSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSallesForExistingCinemas();
    }

    private function createSallesForExistingCinemas(): void
    {
        $cinemas = Cinema::all();

        if ($cinemas->isEmpty()) {
            $this->command->warn('Aucun cinéma trouvé. Exécutez d\'abord CinemaSeeder.');

            return;
        }

        foreach ($cinemas as $cinema) {
            $this->createSallesForCinema($cinema);
        }
    }

    private function createSallesForCinema(Cinema $cinema): void
    {
        $nombreSalles = rand(3, 8); // Chaque cinéma a entre 3 et 8 salles

        for ($i = 1; $i <= $nombreSalles; $i++) {
            $typeSalle = $this->determineSalleType($i, $nombreSalles);

            $salle = match ($typeSalle) {
                'premium' => Salle::factory()->premium()->make(),
                'grande'  => Salle::factory()->grande()->make(),
                'petite'  => Salle::factory()->petite()->make(),
                default   => Salle::factory()->make(),
            };

            // Assurer l'unicité du numéro de salle par cinéma (dual FK pattern)
            $salle->cinema_db_id = $cinema->db_id;  // FK technique pour performance
            $salle->cinema_uuid  = $cinema->uuid;   // FK business pour domaine

            // Nommer la salle selon son type
            $salle->nom = match ($typeSalle) {
                'premium' => "Salle Premium $i",
                'grande'  => "Grande Salle $i",
                'petite'  => "Salle Intime $i",
                default   => "Salle $i",
            };

            $salle->save();
        }

        $this->command->info("✓ {$nombreSalles} salles créées pour {$cinema->nom}");
    }

    private function determineSalleType(int $numeroSalle, int $totalSalles): string
    {
        // Première salle souvent premium dans les grands cinémas
        if ($numeroSalle === 1 && $totalSalles >= 5) {
            return 'premium';
        }

        // Quelques grandes salles
        if ($numeroSalle <= 3 && $totalSalles >= 6) {
            return 'grande';
        }

        // Dernières salles souvent plus petites
        if ($numeroSalle > $totalSalles - 2) {
            return 'petite';
        }

        // Mix par défaut
        return 'standard';
    }
}
