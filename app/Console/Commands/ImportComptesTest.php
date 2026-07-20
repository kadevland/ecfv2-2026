<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Ramsey\Uuid\Uuid;
use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Infrastructure\Database\Models\Profiles\UserProfil;

class ImportComptesTest extends Command
{
    protected $signature = 'comptes:import {--clear : Clear existing test accounts first}';

    protected $description = 'Import test accounts from COMPTES-TEST-ECF.md (direct model usage, no CQRS)';

    private array $comptesTest = [
        // Client 1 - Marie Martin
        [
            'type'           => 'client',
            'nom'            => 'Martin',
            'prenom'         => 'Marie',
            'email'          => 'marie.martin@gmail.com',
            'password'       => 'Client123!@#',
            'telephone'      => '0612345678',
            'date_naissance' => '1990-05-15',
        ],
        // Client 2 - Thomas Dubois
        [
            'type'           => 'client',
            'nom'            => 'Dubois',
            'prenom'         => 'Thomas',
            'email'          => 'thomas.dubois@outlook.com',
            'password'       => 'Client123!@#',
            'telephone'      => '0687654321',
            'date_naissance' => '1985-09-22',
        ],
        // Employé 1 - Jean Dupont (Lille)
        [
            'type'      => 'employee',
            'nom'       => 'Dupont',
            'prenom'    => 'Jean',
            'email'     => 'jean.dupont@cinephoria-lille.fr',
            'password'  => 'Employe123!@#',
            'telephone' => '0320123456',
            'cinema'    => 'Lille',
        ],
        // Employé 2 - Sophie Bernard (Paris)
        [
            'type'      => 'employee',
            'nom'       => 'Bernard',
            'prenom'    => 'Sophie',
            'email'     => 'sophie.bernard@cinephoria-paris.fr',
            'password'  => 'Employe123!@#',
            'telephone' => '0142123456',
            'cinema'    => 'Paris',
        ],
        // Admin - Alexandre Moreau
        [
            'type'      => 'admin',
            'nom'       => 'Moreau',
            'prenom'    => 'Alexandre',
            'email'     => 'admin@cinephoria.fr',
            'password'  => 'Admin123!@#',
            'telephone' => '0156789012',
        ],
    ];

    public function handle(): int
    {
        $this->info('👥 Import des comptes de test ECF...');

        if ($this->option('clear')) {
            $this->clearTestAccounts();
        }

        $successCount = 0;
        $errorCount   = 0;

        foreach ($this->comptesTest as $compteData) {
            try {
                $this->info("📝 Création compte: {$compteData['prenom']} {$compteData['nom']} ({$compteData['type']})");

                $this->createAccount($compteData);
                $successCount++;

            } catch (Exception $e) {
                $this->error("❌ Erreur pour {$compteData['email']}: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('👥 Import comptes terminé!');
        $this->info("✅ Comptes créés avec succès: {$successCount}");

        if ($errorCount > 0) {
            $this->warn("⚠️  Comptes avec erreurs: {$errorCount}");
        }

        $this->newLine();
        $this->line('📱 <info>URLs de test :</info>');
        $this->line('   Connexion: <comment>http://localhost/connexion</comment>');
        $this->line('   Admin: <comment>http://localhost/admin</comment>');
        $this->line('   Employé: <comment>http://localhost/gestion</comment>');

        return $errorCount > 0 ? 1 : 0;
    }

    private function clearTestAccounts(): void
    {
        $this->warn('🗑️  Suppression des comptes de test existants...');

        $testEmails = array_column($this->comptesTest, 'email');

        // Trouver les users via leurs credentials (qui ont l'email)
        $credentials = UserCredential::whereIn('email', $testEmails)->with('user')->get();

        foreach ($credentials as $credential) {
            if ($credential->user) {
                // Supprimer les relations en cascade
                $credential->user->credential()->delete();
                $credential->user->clientProfile()->delete();
                $credential->user->employeeProfile()->delete();
                $credential->user->delete();
            }
        }

        $this->info('✅ Comptes de test supprimés');
    }

    private function createAccount(array $data): void
    {
        // 1. Créer User principal avec UUID v7
        $userId = Uuid::uuid7()->toString();

        $user = User::create([
            'id'        => $userId,
            'type'      => UserType::from($data['type']),
            'is_active' => true,
        ]);

        // 2. Créer UserCredential (mot de passe) avec user_db_id
        UserCredential::create([
            'user_db_id'    => $user->db_id,
            'user_uuid'     => $userId,
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
        ]);

        // 3. Créer UserProfil (informations détaillées)
        $profilData = [
            'uuid'       => Uuid::uuid7()->toString(),
            'user_db_id' => $user->db_id,
            'user_uuid'  => $userId,
            'type'       => $data['type'],
            'nom'        => $data['nom'],
            'prenom'     => $data['prenom'],
            'email'      => $data['email'],
            'telephone'  => $data['telephone'] ?? null,
            'newsletter' => false,
        ];

        // Ajouter date de naissance si client
        if (isset($data['date_naissance'])) {
            $profilData['date_naissance'] = $data['date_naissance'];
        }

        UserProfil::create($profilData);

        // 4. Créer EmployeeProfile si c'est un employé ou admin
        if (in_array($data['type'], ['employee', 'admin'])) {
            $this->createEmployeeProfile($user, $userId, $data);
        }

        // 5. Créer Emploi pour les employés et admins
        if (in_array($data['type'], ['employee', 'admin'])) {
            $this->createEmploi($user, $userId, $data);
        }

        $this->line("  ✅ {$data['prenom']} {$data['nom']} - <info>{$data['email']}</info> ({$data['type']})");
    }

    private function createEmployeeProfile($user, string $userId, array $data): void
    {
        // Trouver le cinéma par nom si spécifié
        $cinemaId = null;
        if (isset($data['cinema'])) {
            $cinema = \Illuminate\Support\Facades\DB::table('cinema.cinemas')
                ->where('nom', 'LIKE', '%' . $data['cinema'] . '%')
                ->first();
            $cinemaId = $cinema?->uuid;
        }

        // Créer le profil employé
        \Illuminate\Support\Facades\DB::table('profiles.employee_profiles')->insert([
            'user_db_id'              => $user->db_id,
            'user_uuid'               => $userId,
            'nom'                     => $data['nom'],
            'prenom'                  => $data['prenom'],
            'email_professionnel'     => $data['email'],
            'telephone_professionnel' => $data['telephone'] ?? null,
            'numero_employe'          => 'EMP-' . strtoupper(substr($data['nom'], 0, 3)) . '-' . date('Y'),
            'date_embauche'           => now()->format('Y-m-d'),
            'poste'                   => $data['type'] === 'admin' ? 'Administrateur' : 'Employé',
            'departement'             => 'Général',
            'cinema_id'               => $cinemaId,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    }

    private function createEmploi($user, string $userId, array $data): void
    {
        // Récupérer le profil utilisateur créé
        $userProfil = \App\Infrastructure\Database\Models\Profiles\UserProfil::where('user_uuid', $userId)->first();
        if (!$userProfil) {
            return;
        }

        // Trouver le cinéma par nom si spécifié
        $cinemaId   = null;
        $cinemaDbId = null;
        if (isset($data['cinema'])) {
            $cinema = \Illuminate\Support\Facades\DB::table('cinema.cinemas')
                ->where('nom', 'LIKE', '%' . $data['cinema'] . '%')
                ->first();
            if ($cinema) {
                $cinemaId   = $cinema->uuid;
                $cinemaDbId = $cinema->db_id;
            }
        }

        // Si pas de cinéma trouvé, prendre le premier cinéma actif
        if (!$cinemaId) {
            $cinema = \Illuminate\Support\Facades\DB::table('cinema.cinemas')
                ->where('est_actif', true)
                ->first();
            if ($cinema) {
                $cinemaId   = $cinema->uuid;
                $cinemaDbId = $cinema->db_id;
            }
        }

        // Déterminer les infos emploi selon le type
        $emploiData = $this->getEmploiDataByType($data['type']);

        // Créer l'emploi
        \Illuminate\Support\Facades\DB::table('employees.emplois')->insert([
            'uuid'                       => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'user_profil_db_id'          => $userProfil->db_id,
            'user_profil_uuid'           => $userProfil->uuid,
            'cinema_db_id'               => $cinemaDbId,
            'cinema_uuid'                => $cinemaId,
            'titre_poste'                => $emploiData['titre_poste'],
            'description'                => $emploiData['description'],
            'categorie'                  => $emploiData['categorie'],
            'niveau'                     => $emploiData['niveau'],
            'type_contrat'               => 'CDI',
            'temps_travail'              => 'TEMPS_PLEIN',
            'salaire_min_ht_centimes'    => $emploiData['salaire_min'],
            'salaire_max_ht_centimes'    => $emploiData['salaire_max'],
            'devise'                     => 'EUR',
            'periodicite_salaire'        => 'MENSUEL',
            'travail_weekend'            => $emploiData['travail_weekend'],
            'travail_feries'             => $emploiData['travail_feries'],
            'travail_soiree'             => $emploiData['travail_soiree'],
            'encadrement_equipe'         => $emploiData['encadrement_equipe'],
            'nombre_personnes_encadrees' => $emploiData['nombre_personnes_encadrees'],
            'statut'                     => 'ACTIF',
            'recrutement_ouvert'         => false,
            'date_creation_poste'        => now(),
            'date_embauche'              => now(),
            'created_at'                 => now(),
            'updated_at'                 => now(),
        ]);
    }

    private function getEmploiDataByType(string $type): array
    {
        return match ($type) {
            'admin' => [
                'titre_poste'                => 'Administrateur',
                'description'                => 'Administration générale de la chaîne de cinémas',
                'categorie'                  => 'DIRECTION',
                'niveau'                     => 'DIRECTEUR',
                'salaire_min'                => 400000, // 4000€
                'salaire_max'                => 600000, // 6000€
                'travail_weekend'            => false,
                'travail_feries'             => false,
                'travail_soiree'             => false,
                'encadrement_equipe'         => true,
                'nombre_personnes_encadrees' => 10,
            ],
            'employee' => [
                'titre_poste'                => 'Employé polyvalent',
                'description'                => 'Accueil, billetterie et assistance clientèle',
                'categorie'                  => 'ACCUEIL_BILLETTERIE',
                'niveau'                     => 'CONFIRME',
                'salaire_min'                => 200000, // 2000€
                'salaire_max'                => 280000, // 2800€
                'travail_weekend'            => true,
                'travail_feries'             => true,
                'travail_soiree'             => true,
                'encadrement_equipe'         => false,
                'nombre_personnes_encadrees' => 0,
            ],
            default => [
                'titre_poste'                => 'Employé',
                'description'                => 'Poste standard',
                'categorie'                  => 'ADMINISTRATIF',
                'niveau'                     => 'JUNIOR',
                'salaire_min'                => 180000, // 1800€
                'salaire_max'                => 220000, // 2200€
                'travail_weekend'            => false,
                'travail_feries'             => false,
                'travail_soiree'             => false,
                'encadrement_equipe'         => false,
                'nombre_personnes_encadrees' => 0,
            ],
        };
    }
}
