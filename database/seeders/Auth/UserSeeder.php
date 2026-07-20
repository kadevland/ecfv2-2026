<?php

declare(strict_types=1);

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use App\Infrastructure\Database\Models\Auth\User;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Only seeds test data in local/testing environments.
     */
    public function run(): void
    {
        // Pas de seeders en production - utiliser les commandes artisan dédiées
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        // Admin de test
        User::factory()
            ->admin()
            ->create();

        // Employés de test
        User::factory()
            ->employee()
            ->count(3)
            ->create();

        // Clients actifs de test
        User::factory()
            ->client()
            ->count(10)
            ->create();

        // Quelques clients supprimés pour tester RGPD
        User::factory()
            ->deleted()
            ->count(2)
            ->create();
    }
}
