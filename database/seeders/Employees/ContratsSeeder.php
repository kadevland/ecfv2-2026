<?php

declare(strict_types=1);

namespace Database\Seeders\Employees;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;

final class ContratsSeeder extends Seeder
{
    /**
     * Seed contrats table linking employees to job positions
     */
    public function run(): void
    {
        $this->command->info('📋 Création des contrats employés...');

        // Récupérer tous les employés avec leur profil
        $employees = User::where('type', 'employee')
            ->with('profil')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('⚠️ Aucun employé trouvé. Les contrats ne seront pas créés.');

            return;
        }

        // Récupérer tous les emplois disponibles
        $emplois = DB::table(EmploiSchema::FULL_TABLE)->get();

        if ($emplois->isEmpty()) {
            $this->command->warn('⚠️ Aucun emploi trouvé. Veuillez d\'abord exécuter EmploisSeeder.');

            return;
        }

        $contratsCreated = 0;
        $contractNumber  = 1;

        foreach ($employees as $employee) {
            // Vérifier que l'employé a un profil
            if (!$employee->profil) {
                $this->command->warn("⚠️ Employé {$employee->id} sans profil - contrat non créé");

                continue;
            }

            // Assigner un emploi au hasard (ou selon une logique métier)
            $emploi = $this->assignEmploiToEmployee($employee, $emplois);

            if (!$emploi) {
                $this->command->warn("⚠️ Aucun emploi approprié trouvé pour {$employee->profil->prenom} {$employee->profil->nom}");

                continue;
            }

            // Générer les données du contrat
            $contratData = $this->generateContratData($employee, $emploi, $contractNumber);

            DB::table(ContratSchema::FULL_TABLE)->insert($contratData);

            $contratsCreated++;
            $contractNumber++;

            $this->command->line("✓ Contrat créé: {$employee->profil->prenom} {$employee->profil->nom} → {$emploi->titre_poste}");
        }

        $this->command->info("✅ {$contratsCreated} contrats employés créés");
    }

    /**
     * Assign appropriate job to employee based on their profile
     */
    private function assignEmploiToEmployee($employee, $emplois)
    {
        // Logique d'assignation basée sur le nom/prénom (pour les données de test)
        $nom    = strtolower($employee->profil->nom ?? '');
        $prenom = strtolower($employee->profil->prenom ?? '');

        // Assignation intelligente basée sur les noms des employés de test
        if (str_contains($nom, 'caissier') || str_contains($prenom, 'caissier')) {
            return $emplois->where('titre_poste', 'Caissier')->first();
        }

        if (str_contains($nom, 'manager') || str_contains($prenom, 'manager')) {
            return $emplois->where('titre_poste', 'Manager')->first();
        }

        if (str_contains($nom, 'directeur') || str_contains($prenom, 'directeur')) {
            return $emplois->where('titre_poste', 'Directeur')->first();
        }

        if (str_contains($nom, 'projectionniste') || str_contains($prenom, 'projectionniste')) {
            return $emplois->where('titre_poste', 'Projectionniste')->first();
        }

        if (str_contains($nom, 'securite') || str_contains($prenom, 'securite')) {
            return $emplois->where('titre_poste', 'Agent de sécurité')->first();
        }

        if (str_contains($nom, 'entretien') || str_contains($prenom, 'entretien')) {
            return $emplois->where('titre_poste', 'Agent d\'entretien')->first();
        }

        // Assignation par défaut selon la hiérarchie
        // D'abord les postes à responsabilité (moins nombreux)
        $directeurCount = DB::table(ContratSchema::FULL_TABLE . ' as c')
            ->join(EmploiSchema::FULL_TABLE . ' as e', 'c.' . ContratSchema::EMPLOI_UUID, '=', 'e.' . EmploiSchema::ID)
            ->where('e.' . EmploiSchema::TITRE_POSTE, 'Directeur')
            ->where('c.' . ContratSchema::STATUT, 'ACTIF')
            ->count();

        if ($directeurCount < 1) {
            return $emplois->where('titre_poste', 'Directeur')->first();
        }

        $managerCount = DB::table(ContratSchema::FULL_TABLE . ' as c')
            ->join(EmploiSchema::FULL_TABLE . ' as e', 'c.' . ContratSchema::EMPLOI_UUID, '=', 'e.' . EmploiSchema::ID)
            ->where('e.' . EmploiSchema::TITRE_POSTE, 'Manager')
            ->where('c.' . ContratSchema::STATUT, 'ACTIF')
            ->count();

        if ($managerCount < 2) {
            return $emplois->where('titre_poste', 'Manager')->first();
        }

        // Sinon assignation aléatoire parmi les autres postes
        $autresPostes = ['Caissier', 'Projectionniste', 'Agent de sécurité', 'Agent d\'entretien'];
        $posteChoisi  = fake()->randomElement($autresPostes);

        return $emplois->where('titre_poste', $posteChoisi)->first();
    }

    /**
     * Generate contract data for employee
     */
    private function generateContratData($employee, $emploi, int $contractNumber): array
    {
        $dateDebut   = fake()->dateTimeBetween('-2 years', '-1 month');
        $salaireBrut = fake()->numberBetween(
            $emploi->salaire_min_ht_centimes,
            $emploi->salaire_max_ht_centimes
        );

        // Déterminer le type de contrat (mostly CDI pour les employés permanents)
        $typeContrat = fake()->randomElement(['CDI', 'CDI', 'CDI', 'CDI', 'CDD']); // 80% CDI

        return [
            ContratSchema::ID             => fake()->uuid(),
            ContratSchema::NUMERO_CONTRAT => 'CTR-2025-' . str_pad((string) $contractNumber, 6, '0', STR_PAD_LEFT),
            ContratSchema::USER_PROFIL_ID => $employee->profil->db_id,
            ContratSchema::USER_UUID      => (string) $employee->id,
            ContratSchema::EMPLOI_DB_ID   => $emploi->db_id,
            ContratSchema::EMPLOI_UUID    => $emploi->uuid,
            ContratSchema::TYPE_CONTRAT   => $typeContrat,
            ContratSchema::STATUT         => 'ACTIF',
            ContratSchema::DATE_DEBUT     => $dateDebut->format('Y-m-d'),
            ContratSchema::DATE_FIN       => $typeContrat === 'CDD' ?
                fake()->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d') : null,
            ContratSchema::DATE_SIGNATURE         => fake()->dateTimeBetween($dateDebut, $dateDebut->format('Y-m-d') . ' +1 week')->format('Y-m-d'),
            ContratSchema::DATE_FIN_PERIODE_ESSAI => fake()->dateTimeBetween($dateDebut, $dateDebut->format('Y-m-d') . ' +3 months')->format('Y-m-d'),
            ContratSchema::TEMPS_TRAVAIL          => $emploi->temps_travail,
            ContratSchema::HEURES_HEBDOMADAIRES   => $emploi->temps_travail === 'TEMPS_PLEIN' ? 35.00 :
                fake()->randomFloat(2, 20, 30),
            ContratSchema::JOURS_CONGES_ANNUELS      => 25,
            ContratSchema::SALAIRE_BRUT_HT_CENTIMES  => $salaireBrut,
            ContratSchema::DEVISE                    => 'EUR',
            ContratSchema::PERIODICITE_SALAIRE       => 'MENSUEL',
            ContratSchema::PRIME_ANCIENNETE_CENTIMES => fake()->boolean(40) ?
                fake()->numberBetween(5000, 15000) : 0, // 40% chance d'avoir une prime ancienneté
            ContratSchema::PRIME_PERFORMANCE_CENTIMES => fake()->boolean(30) ?
                fake()->numberBetween(5000, 25000) : 0, // 30% chance d'avoir une prime performance
            ContratSchema::AVANTAGES_NATURE_CENTIMES => fake()->boolean(20) ?
                fake()->numberBetween(2000, 8000) : 0, // 20% chance d'avoir des avantages nature
            ContratSchema::DETAIL_AVANTAGES          => $this->generateAvantages(),
            ContratSchema::CLAUSE_NON_CONCURRENCE    => fake()->boolean(15), // 15% avec clause non-concurrence
            ContratSchema::CLAUSE_CONFIDENTIALITE    => true, // Toujours true pour cinéma
            ContratSchema::CLAUSE_MOBILITE           => fake()->boolean(25), // 25% avec clause mobilité
            ContratSchema::PREAVIS_JOURS             => $this->calculatePreavis($emploi->niveau),
            ContratSchema::HORAIRES_STANDARDS        => $this->generateHoraires($emploi),
            ContratSchema::TRAVAIL_WEEKEND           => $emploi->travail_weekend,
            ContratSchema::TRAVAIL_FERIES            => $emploi->travail_feries,
            ContratSchema::TRAVAIL_NUIT              => $emploi->travail_soiree,
            ContratSchema::BUDGET_FORMATION_CENTIMES => fake()->numberBetween(50000, 200000), // 500€ à 2000€
            ContratSchema::OBJECTIFS_POSTE           => $this->generateObjectifs($emploi->titre_poste),
            ContratSchema::DATE_PROCHAINE_EVALUATION => fake()->dateTimeBetween('+3 months', '+1 year')->format('Y-m-d'),
            ContratSchema::NUMERO_SECURITE_SOCIALE   => fake()->regexify('[1-2][0-9]{14}'),
            ContratSchema::CONVENTION_COLLECTIVE     => 'Convention collective du cinéma',
            ContratSchema::CLASSIFICATION_POSTE      => $this->getClassification($emploi->niveau),
            ContratSchema::COEFFICIENT_HIERARCHIQUE  => $this->getCoefficient($emploi->niveau),
            ContratSchema::VERSION                   => 1,
            ContratSchema::CONTRAT_PARENT_ID         => null,
            ContratSchema::MOTIF_MODIFICATION        => null,
            ContratSchema::DATE_FIN_EFFECTIVE        => null,
            ContratSchema::MOTIF_FIN                 => null,
            ContratSchema::COMMENTAIRE_FIN           => null,
            ContratSchema::DOCUMENT_PDF_URL          => null,
            ContratSchema::SIGNATURE_EMPLOYE_URL     => null,
            ContratSchema::SIGNATURE_EMPLOYEUR_URL   => null,
            ContratSchema::DATE_SIGNATURE_EMPLOYE    => null,
            ContratSchema::DATE_SIGNATURE_EMPLOYEUR  => null,
            ContratSchema::NOTES_RH                  => fake()->boolean(30) ? fake()->sentence() : null,
            ContratSchema::METADONNEES_CONTRAT       => json_encode([
                'created_by_seeder' => true,
                'seeder_version'    => '1.0',
            ]),
            ContratSchema::CREATED_AT => now(),
            ContratSchema::UPDATED_AT => now(),
        ];
    }

    private function generateAvantages(): string
    {
        $avantages = [];

        if (fake()->boolean(60)) {
            $avantages[] = [
                'type'             => 'tickets_restaurant',
                'description'      => 'Tickets restaurant',
                'valeur_mensuelle' => fake()->numberBetween(8000, 12000), // 80-120€
            ];
        }

        if (fake()->boolean(40)) {
            $avantages[] = [
                'type'             => 'transport',
                'description'      => 'Remboursement transport en commun',
                'valeur_mensuelle' => fake()->numberBetween(5000, 8000), // 50-80€
            ];
        }

        if (fake()->boolean(80)) {
            $avantages[] = [
                'type'             => 'cinema',
                'description'      => 'Séances gratuites cinéma',
                'valeur_mensuelle' => fake()->numberBetween(3000, 6000), // 30-60€
            ];
        }

        return json_encode($avantages);
    }

    private function calculatePreavis(string $niveau): int
    {
        return match ($niveau) {
            'DIRECTEUR' => 90,
            'SENIOR', 'MANAGER' => 60,
            'CONFIRME' => 30,
            'JUNIOR', 'STAGIAIRE' => 15,
            default => 30
        };
    }

    private function generateHoraires($emploi): string
    {
        $horaires = [
            'lundi'    => ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type],
            'mardi'    => ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type],
            'mercredi' => ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type],
            'jeudi'    => ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type],
            'vendredi' => ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type],
        ];

        if ($emploi->travail_weekend) {
            $horaires['samedi']   = ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type];
            $horaires['dimanche'] = ['debut' => $emploi->heure_debut_type, 'fin' => $emploi->heure_fin_type];
        }

        return json_encode($horaires);
    }

    private function generateObjectifs(string $poste): string
    {
        $objectifs = match ($poste) {
            'Caissier'           => 'Accueil de qualité, vente additionnelle confiseries, gestion caisse sans erreur',
            'Manager'            => 'Management équipe, atteinte objectifs chiffre d\'affaires, satisfaction client',
            'Projectionniste'    => 'Zéro incident technique, maintenance préventive, respect planning',
            'Agent de sécurité'  => 'Sécurité des biens et personnes, prévention incidents, rapport mensuel',
            'Agent d\'entretien' => 'Propreté espaces, maintenance légère, respect protocoles hygiène',
            'Directeur'          => 'Rentabilité cinéma, développement commercial, management global',
            default              => 'Objectifs à définir lors de l\'entretien annuel'
        };

        return $objectifs;
    }

    private function getClassification(string $niveau): string
    {
        return match ($niveau) {
            'DIRECTEUR' => 'Cadre supérieur',
            'MANAGER'   => 'Cadre intermédiaire',
            'SENIOR'    => 'Agent de maîtrise',
            'CONFIRME'  => 'Employé qualifié',
            'JUNIOR'    => 'Employé',
            'STAGIAIRE' => 'Stagiaire',
            default     => 'Employé'
        };
    }

    private function getCoefficient(string $niveau): int
    {
        return match ($niveau) {
            'DIRECTEUR' => 500,
            'MANAGER'   => 400,
            'SENIOR'    => 350,
            'CONFIRME'  => 300,
            'JUNIOR'    => 250,
            'STAGIAIRE' => 200,
            default     => 250
        };
    }
}
