<?php

declare(strict_types=1);

namespace Database\Seeders\Profiles;

use Illuminate\Database\Seeder;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Profiles\UserRgpdProfil;

final class UserRgpdProfilSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRgpdDeletedProfiles();
    }

    private function createRgpdDeletedProfiles(): void
    {
        // Créer des utilisateurs supprimés avec leurs profils RGPD
        for ($i = 0; $i < 5; $i++) {
            // Utilisateur supprimé (pas de profil dans user_profil)
            $deletedUser = User::factory()->deleted()->create([
                'is_active' => false,
            ]);

            // Profil de substitution RGPD avec données minimales
            UserRgpdProfil::factory()->create([
                'user_uuid_original'         => $deletedUser->id,
                'nom_substitution'           => 'Utilisateur Supprimé',
                'prenom_substitution'        => 'RGPD',
                'email_substitution'         => 'utilisateur.supprime+' . $i . '@rgpd.local',
                'date_suppression_demandee'  => now()->subDays(rand(30, 365)),
                'date_suppression_effective' => now()->subDays(rand(1, 30)),
                'raison_suppression'         => fake()->randomElement([
                    'DROIT_OUBLI',
                    'DEMANDE_CLIENT',
                    'INACTIVITE',
                ]),
                'operateur_suppression'          => 'Admin RGPD',
                'commentaire_interne'            => 'Suppression automatique suite à demande utilisateur',
                'avait_reservations'             => fake()->boolean(70),
                'nombre_reservations_historique' => fake()->numberBetween(0, 25),
            ]);
        }

        // Cas particulier : utilisateur ayant eu beaucoup de réservations
        $heavyUser = User::factory()->deleted()->create([
            'is_active' => false,
        ]);

        UserRgpdProfil::factory()->create([
            'user_uuid_original'             => $heavyUser->id,
            'nom_substitution'               => 'Client Régulier',
            'prenom_substitution'            => 'SUPPRIMÉ',
            'email_substitution'             => 'client.regulier.supprime@rgpd.local',
            'date_suppression_demandee'      => now()->subDays(15),
            'date_suppression_effective'     => now()->subDays(7),
            'raison_suppression'             => 'DROIT_OUBLI',
            'operateur_suppression'          => 'Sophie Manager',
            'commentaire_interne'            => 'Client régulier avec 45+ réservations. Conservation données statistiques.',
            'avait_reservations'             => true,
            'nombre_reservations_historique' => 47,
        ]);

        // Cas particulier : violation CGU
        $violationUser = User::factory()->deleted()->create([
            'is_active' => false,
        ]);

        UserRgpdProfil::factory()->create([
            'user_uuid_original'             => $violationUser->id,
            'nom_substitution'               => 'Utilisateur Banni',
            'prenom_substitution'            => 'CGU',
            'email_substitution'             => 'utilisateur.banni@rgpd.local',
            'date_suppression_demandee'      => now()->subDays(90),
            'date_suppression_effective'     => now()->subDays(89),
            'raison_suppression'             => 'VIOLATION_CGU',
            'operateur_suppression'          => 'Modération Auto',
            'commentaire_interne'            => 'Comportement inapproprié signalé. Bannissement définitif.',
            'avait_reservations'             => false,
            'nombre_reservations_historique' => 0,
        ]);

        $this->command->info('✅ ' . (5 + 2) . ' profils RGPD créés (utilisateurs supprimés)');
    }
}
