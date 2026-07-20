<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Cinema\FilmSeeder;
use Database\Seeders\Cinema\SalleSeeder;
use Database\Seeders\Cinema\CinemaSeeder;
use Database\Seeders\Cinema\SeanceSeeder;
use Database\Seeders\Employees\EmploisSeeder;
use Database\Seeders\Employees\ContratsSeeder;
use Database\Seeders\Profiles\UserProfilSeeder;
use Database\Seeders\Profiles\UserRgpdProfilSeeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seedage des données de test Cinéphoria...');

        $this->call([
            // Ordre important : Users d'abord
            UserSeeder::class,

            // Ensuite les cinémas (requis pour les employés et salles)
            CinemaSeeder::class,

            // Films (indépendants)
            FilmSeeder::class,

            // Salles (dépendent des cinémas)
            SalleSeeder::class,

            // Séances (dépendent des films et salles)
            SeanceSeeder::class,

            // Profils utilisateurs (dépendent des users et cinémas)
            UserProfilSeeder::class,

            // Définitions d'emplois (dépendent des cinémas)
            EmploisSeeder::class,

            // Contrats employés (dépendent des users avec profils et emplois)
            ContratsSeeder::class,

            // Profils RGPD (utilisateurs supprimés)
            UserRgpdProfilSeeder::class,
        ]);

        $this->command->info('✅ Seedage terminé avec succès!');
        $this->displayStats();
    }

    private function displayStats(): void
    {
        $stats = [
            'Cinémas'                => \App\Infrastructure\Database\Models\Cinema\Cinema::count(),
            'Films'                  => \App\Infrastructure\Database\Models\Cinema\Film::count(),
            'Salles'                 => \App\Infrastructure\Database\Models\Cinema\Salle::count(),
            'Séances'                => \App\Infrastructure\Database\Models\Cinema\Seance::count(),
            'Utilisateurs'           => \App\Infrastructure\Database\Models\Auth\User::count(),
            'Profils utilisateurs'   => \App\Infrastructure\Database\Models\Profiles\UserProfil::count(),
            'Emplois'                => \Illuminate\Support\Facades\DB::table('employees.emplois')->count(),
            'Contrats employés'      => \Illuminate\Support\Facades\DB::table('employees.contrats')->count(),
            'Profils RGPD supprimés' => \App\Infrastructure\Database\Models\Profiles\UserRgpdProfil::count(),
        ];

        $this->command->info('📊 Statistiques des données créées:');
        foreach ($stats as $type => $count) {
            $this->command->line("   • {$type}: {$count}");
        }
    }
}
