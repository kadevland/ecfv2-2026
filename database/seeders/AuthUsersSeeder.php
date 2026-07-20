<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyer les tables existantes
        DB::table('user_credentials')->truncate();
        DB::table('users')->truncate();
        DB::table('user_profils')->truncate();

        // Créer un admin
        $adminId = Str::uuidV7()->toString();
        DB::table('users')->insert([
            'id'         => $adminId,
            'nom'        => 'Admin',
            'prenom'     => 'Super',
            'email'      => 'admin@cinephoria.fr',
            'role'       => 'administrateur',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_credentials')->insert([
            'id'            => Str::uuidV7()->toString(),
            'user_id'       => $adminId,
            'email'         => 'admin@cinephoria.fr',
            'password_hash' => Hash::make('Admin123!@#'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('user_profils')->insert([
            'id'         => Str::uuidV7()->toString(),
            'user_id'    => $adminId,
            'nom'        => 'Admin',
            'prenom'     => 'Super',
            'email'      => 'admin@cinephoria.fr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer un employé
        $employeeId = Str::uuidV7()->toString();
        DB::table('users')->insert([
            'id'         => $employeeId,
            'nom'        => 'Dupont',
            'prenom'     => 'Jean',
            'email'      => 'employe@cinephoria.fr',
            'role'       => 'employe',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_credentials')->insert([
            'id'            => Str::uuidV7()->toString(),
            'user_id'       => $employeeId,
            'email'         => 'employe@cinephoria.fr',
            'password_hash' => Hash::make('Employe123!@#'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('user_profils')->insert([
            'id'         => Str::uuidV7()->toString(),
            'user_id'    => $employeeId,
            'nom'        => 'Dupont',
            'prenom'     => 'Jean',
            'email'      => 'employe@cinephoria.fr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer un client
        $clientId = Str::uuidV7()->toString();
        DB::table('users')->insert([
            'id'         => $clientId,
            'nom'        => 'Martin',
            'prenom'     => 'Marie',
            'email'      => 'client@example.com',
            'role'       => 'client',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_credentials')->insert([
            'id'            => Str::uuidV7()->toString(),
            'user_id'       => $clientId,
            'email'         => 'client@example.com',
            'password_hash' => Hash::make('Client123!@#'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('user_profils')->insert([
            'id'         => Str::uuidV7()->toString(),
            'user_id'    => $clientId,
            'nom'        => 'Martin',
            'prenom'     => 'Marie',
            'email'      => 'client@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Utilisateurs de test créés avec succès :');
        $this->command->info('');
        $this->command->info('🔐 Comptes de test :');
        $this->command->info('');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Administrateur', 'admin@cinephoria.fr', 'Admin123!@#'],
                ['Employé', 'employe@cinephoria.fr', 'Employe123!@#'],
                ['Client', 'client@example.com', 'Client123!@#'],
            ]
        );
    }
}
