<?php

declare(strict_types=1);

namespace Database\Seeders\Profiles;

use Illuminate\Database\Seeder;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Profiles\UserProfil;

final class UserProfilSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRealUserProfils();
        $this->createFakeUserProfils();
    }

    private function createRealUserProfils(): void
    {
        // Profil client standard actif
        $clientUser1 = User::factory()->client()->create([
            'is_active' => true,
        ]);

        UserProfil::factory()->create([
            'user_db_id'     => $clientUser1->db_id,
            'user_uuid'      => $clientUser1->id,
            'type'           => $clientUser1->type->value,
            'prenom'         => 'Jean',
            'nom'            => 'Dupont',
            'email'          => 'jean.dupont@email.com',
            'telephone'      => '+33123456789',
            'date_naissance' => '1985-03-15',
            'sexe'           => 'M',
            'adresse'        => [
                'rue'         => '123 Rue de la Paix',
                'ville'       => 'Paris',
                'code_postal' => '75001',
                'pays'        => 'France',
            ],
            'preferences' => [
                'newsletter'          => true,
                'notifications_email' => true,
                'notifications_sms'   => true,
                'langue'              => 'fr',
            ],
            'newsletter' => true,
        ]);

        // Profil cliente avec adresse différente
        $clientUser2 = User::factory()->client()->create([
            'is_active' => true,
        ]);

        UserProfil::factory()->create([
            'user_db_id'     => $clientUser2->db_id,
            'user_uuid'      => $clientUser2->id,
            'type'           => $clientUser2->type->value,
            'prenom'         => 'Marie',
            'nom'            => 'Martin',
            'email'          => 'marie.martin@email.com',
            'telephone'      => '+33187654321',
            'date_naissance' => '1992-08-22',
            'sexe'           => 'F',
            'adresse'        => [
                'rue'         => '45 Avenue des Champs',
                'ville'       => 'Lyon',
                'code_postal' => '69001',
                'pays'        => 'France',
                'complement'  => 'Appartement 3B',
            ],
            'preferences' => [
                'newsletter'          => false,
                'notifications_email' => true,
                'notifications_sms'   => false,
                'langue'              => 'fr',
            ],
            'newsletter' => false,
        ]);

        // Profil employé manager
        $managerUser = User::factory()->admin()->create([
            'is_active' => true,
        ]);

        UserProfil::factory()->create([
            'user_db_id'     => $managerUser->db_id,
            'user_uuid'      => $managerUser->id,
            'type'           => $managerUser->type->value,
            'prenom'         => 'Sophie',
            'nom'            => 'Manager',
            'email'          => 'sophie.manager@cinephoria.fr',
            'telephone'      => '+33123456700',
            'date_naissance' => '1980-04-12',
            'sexe'           => 'F',
            'adresse'        => [
                'rue'         => '10 Rue du Cinéma',
                'ville'       => 'Paris',
                'code_postal' => '75008',
                'pays'        => 'France',
            ],
            'preferences' => [
                'newsletter'          => false,
                'notifications_email' => true,
                'notifications_sms'   => true,
                'langue'              => 'fr',
            ],
            'newsletter' => false,
        ]);

        // Profil employé caissier
        $employeeUser = User::factory()->employee()->create([
            'is_active' => true,
        ]);

        UserProfil::factory()->create([
            'user_db_id'     => $employeeUser->db_id,
            'user_uuid'      => $employeeUser->id,
            'type'           => $employeeUser->type->value,
            'prenom'         => 'Thomas',
            'nom'            => 'Caissier',
            'email'          => 'thomas.caissier@cinephoria.fr',
            'telephone'      => '+33123456701',
            'date_naissance' => '1995-09-22',
            'sexe'           => 'M',
            'adresse'        => [
                'rue'         => '25 Rue des Employés',
                'ville'       => 'Paris',
                'code_postal' => '75010',
                'pays'        => 'France',
            ],
            'preferences' => [
                'newsletter'          => false,
                'notifications_email' => true,
                'notifications_sms'   => false,
                'langue'              => 'fr',
            ],
            'newsletter' => false,
        ]);

        // Client belge
        $belgianUser = User::factory()->client()->create([
            'is_active' => true,
        ]);

        UserProfil::factory()->create([
            'user_db_id'     => $belgianUser->db_id,
            'user_uuid'      => $belgianUser->id,
            'type'           => $belgianUser->type->value,
            'prenom'         => 'Pierre',
            'nom'            => 'Dubois',
            'email'          => 'pierre.dubois@email.be',
            'telephone'      => '+3225123456',
            'date_naissance' => '1979-12-05',
            'sexe'           => 'M',
            'adresse'        => [
                'rue'         => '12 Grand Place',
                'ville'       => 'Bruxelles',
                'code_postal' => '1000',
                'pays'        => 'Belgique',
            ],
            'preferences' => [
                'newsletter'          => true,
                'notifications_email' => true,
                'notifications_sms'   => false,
                'langue'              => 'fr',
            ],
            'newsletter' => true,
        ]);
    }

    private function createFakeUserProfils(): void
    {
        // Profils clients variés
        $clientUsers = User::factory()->client()->count(15)->create(['is_active' => true]);
        foreach ($clientUsers as $user) {
            UserProfil::factory()->create([
                'user_db_id' => $user->db_id,
                'user_uuid'  => $user->id,
                'type'       => $user->type->value,
            ]);
        }

        // Profils employés variés
        $employeeUsers = User::factory()->employee()->count(8)->create(['is_active' => true]);
        foreach ($employeeUsers as $user) {
            UserProfil::factory()->create([
                'user_db_id' => $user->db_id,
                'user_uuid'  => $user->id,
                'type'       => $user->type->value,
            ]);
        }

        // Profils inactifs
        $inactiveUsers = User::factory()->client()->count(5)->create(['is_active' => false]);
        foreach ($inactiveUsers as $user) {
            UserProfil::factory()->create([
                'user_db_id' => $user->db_id,
                'user_uuid'  => $user->id,
                'type'       => $user->type->value,
            ]);
        }
    }
}
