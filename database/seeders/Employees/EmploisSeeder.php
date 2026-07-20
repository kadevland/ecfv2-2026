<?php

declare(strict_types=1);

namespace Database\Seeders\Employees;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;

final class EmploisSeeder extends Seeder
{
    /**
     * Seed emplois table with job definitions
     */
    public function run(): void
    {
        $this->command->info('🏢 Création des définitions d\'emplois...');

        // Récupérer les cinémas existants avec toutes les colonnes nécessaires
        $cinemas = Cinema::select('db_id', 'uuid', 'nom')->get();

        if ($cinemas->isEmpty()) {
            $this->command->warn('⚠️ Aucun cinéma trouvé. Les emplois ne seront pas créés.');

            return;
        }

        $emploiTypes = [
            [
                'titre_poste'             => 'Caissier',
                'description'             => 'Accueil des clients, vente de billets, encaissement',
                'categorie'               => 'ACCUEIL_BILLETTERIE',
                'niveau'                  => 'JUNIOR',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PLEIN',
                'salaire_min_ht_centimes' => 150000, // 1500€
                'salaire_max_ht_centimes' => 180000, // 1800€
                'experience_minimum_mois' => 0,
                'responsabilites'         => 'Accueil clientèle, vente de billets et confiseries, encaissement, nettoyage de l\'espace d\'accueil',
                'competences_requises'    => [
                    'Sens du service client',
                    'Manipulation de caisse enregistreuse',
                    'Connaissance des films',
                ],
                'travail_weekend'  => true,
                'travail_soiree'   => true,
                'heure_debut_type' => '13:00',
                'heure_fin_type'   => '23:00',
            ],
            [
                'titre_poste'             => 'Manager',
                'description'             => 'Encadrement d\'équipe, gestion opérationnelle',
                'categorie'               => 'DIRECTION',
                'niveau'                  => 'SENIOR',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PLEIN',
                'salaire_min_ht_centimes' => 300000, // 3000€
                'salaire_max_ht_centimes' => 400000, // 4000€
                'experience_minimum_mois' => 24,
                'responsabilites'         => 'Management d\'équipe, planification, gestion des conflits, reporting direction',
                'competences_requises'    => [
                    'Management d\'équipe',
                    'Gestion budgétaire',
                    'Communication',
                    'Organisation',
                ],
                'encadrement_equipe'         => true,
                'nombre_personnes_encadrees' => 8,
                'travail_weekend'            => true,
                'travail_soiree'             => true,
                'heure_debut_type'           => '09:00',
                'heure_fin_type'             => '19:00',
            ],
            [
                'titre_poste'             => 'Projectionniste',
                'description'             => 'Gestion technique des projections',
                'categorie'               => 'PROJECTION',
                'niveau'                  => 'CONFIRME',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PLEIN',
                'salaire_min_ht_centimes' => 200000, // 2000€
                'salaire_max_ht_centimes' => 250000, // 2500€
                'experience_minimum_mois' => 12,
                'responsabilites'         => 'Préparation et lancement des projections, maintenance du matériel, contrôle qualité',
                'competences_requises'    => [
                    'Techniques de projection',
                    'Maintenance matériel',
                    'Gestion planning séances',
                ],
                'travail_weekend'  => true,
                'travail_soiree'   => true,
                'travail_feries'   => true,
                'heure_debut_type' => '12:00',
                'heure_fin_type'   => '24:00',
            ],
            [
                'titre_poste'             => 'Agent d\'entretien',
                'description'             => 'Nettoyage et maintenance des espaces',
                'categorie'               => 'ENTRETIEN',
                'niveau'                  => 'JUNIOR',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PARTIEL',
                'salaire_min_ht_centimes' => 120000, // 1200€
                'salaire_max_ht_centimes' => 150000, // 1500€
                'experience_minimum_mois' => 0,
                'responsabilites'         => 'Nettoyage des salles, hall d\'accueil, sanitaires, maintenance légère',
                'competences_requises'    => [
                    'Techniques de nettoyage',
                    'Utilisation produits d\'entretien',
                ],
                'travail_weekend'  => false,
                'travail_soiree'   => false,
                'heure_debut_type' => '06:00',
                'heure_fin_type'   => '12:00',
            ],
            [
                'titre_poste'             => 'Agent de sécurité',
                'description'             => 'Surveillance et sécurité du cinéma',
                'categorie'               => 'SECURITE',
                'niveau'                  => 'CONFIRME',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PLEIN',
                'salaire_min_ht_centimes' => 170000, // 1700€
                'salaire_max_ht_centimes' => 200000, // 2000€
                'experience_minimum_mois' => 6,
                'responsabilites'         => 'Surveillance des espaces, contrôle d\'accès, gestion des incidents',
                'competences_requises'    => [
                    'Surveillance et sécurité',
                    'Gestion de conflit',
                    'Premiers secours',
                ],
                'formations_requises' => 'Carte professionnelle agent de sécurité',
                'travail_weekend'     => true,
                'travail_soiree'      => true,
                'travail_feries'      => true,
                'heure_debut_type'    => '14:00',
                'heure_fin_type'      => '02:00',
            ],
            [
                'titre_poste'             => 'Directeur',
                'description'             => 'Direction générale du cinéma',
                'categorie'               => 'DIRECTION',
                'niveau'                  => 'DIRECTEUR',
                'type_contrat'            => 'CDI',
                'temps_travail'           => 'TEMPS_PLEIN',
                'salaire_min_ht_centimes' => 450000, // 4500€
                'salaire_max_ht_centimes' => 600000, // 6000€
                'experience_minimum_mois' => 60,
                'responsabilites'         => 'Direction générale, stratégie, gestion budgétaire, relations institutionnelles',
                'competences_requises'    => [
                    'Direction d\'entreprise',
                    'Gestion budgétaire',
                    'Stratégie commerciale',
                    'Relations publiques',
                ],
                'encadrement_equipe'         => true,
                'nombre_personnes_encadrees' => 25,
                'travail_weekend'            => false,
                'travail_soiree'             => false,
                'heure_debut_type'           => '08:00',
                'heure_fin_type'             => '18:00',
            ],
        ];

        foreach ($cinemas as $cinema) {
            foreach ($emploiTypes as $emploiData) {
                DB::table(EmploiSchema::FULL_TABLE)->insert([
                    EmploiSchema::ID                      => fake()->uuid(),
                    EmploiSchema::CINEMA_KEY              => $cinema->db_id,
                    EmploiSchema::CINEMA_ID               => $cinema->uuid,
                    EmploiSchema::TITRE_POSTE             => $emploiData['titre_poste'],
                    EmploiSchema::DESCRIPTION             => $emploiData['description'] ?? null,
                    EmploiSchema::CATEGORIE               => $emploiData['categorie'],
                    EmploiSchema::NIVEAU                  => $emploiData['niveau'],
                    EmploiSchema::TYPE_CONTRAT            => $emploiData['type_contrat'],
                    EmploiSchema::TEMPS_TRAVAIL           => $emploiData['temps_travail'],
                    EmploiSchema::SALAIRE_MIN_HT_CENTIMES => $emploiData['salaire_min_ht_centimes'],
                    EmploiSchema::SALAIRE_MAX_HT_CENTIMES => $emploiData['salaire_max_ht_centimes'],
                    EmploiSchema::DEVISE                  => 'EUR',
                    EmploiSchema::PERIODICITE_SALAIRE     => 'MENSUEL',
                    /** @phpstan-ignore-next-line nullCoalesce.offset */
                    EmploiSchema::COMPETENCES_REQUISES => json_encode($emploiData['competences_requises'] ?? []),
                    EmploiSchema::FORMATIONS_REQUISES  => $emploiData['formations_requises'] ?? null,
                    /** @phpstan-ignore-next-line nullCoalesce.offset */
                    EmploiSchema::EXPERIENCE_MINIMUM_MOIS => $emploiData['experience_minimum_mois'] ?? 0,
                    EmploiSchema::HEURE_DEBUT_TYPE        => $emploiData['heure_debut_type'] ?? null,
                    EmploiSchema::HEURE_FIN_TYPE          => $emploiData['heure_fin_type'] ?? null,
                    EmploiSchema::TRAVAIL_WEEKEND         => $emploiData['travail_weekend'] ?? false,
                    EmploiSchema::TRAVAIL_FERIES          => $emploiData['travail_feries'] ?? false,
                    EmploiSchema::TRAVAIL_SOIREE          => $emploiData['travail_soiree'] ?? false,
                    /** @phpstan-ignore-next-line nullCoalesce.offset */
                    EmploiSchema::RESPONSABILITES            => $emploiData['responsabilites'] ?? null,
                    EmploiSchema::ENCADREMENT_EQUIPE         => $emploiData['encadrement_equipe'] ?? false,
                    EmploiSchema::NOMBRE_PERSONNES_ENCADREES => $emploiData['nombre_personnes_encadrees'] ?? 0,
                    EmploiSchema::STATUT                     => 'ACTIF',
                    EmploiSchema::RECRUTEMENT_OUVERT         => fake()->boolean(30), // 30% chance d'être ouvert au recrutement
                    EmploiSchema::DATE_CREATION_POSTE        => fake()->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d'),
                    EmploiSchema::CODE_POSTE                 => strtoupper(substr($emploiData['titre_poste'], 0, 3)) . '-' . $cinema->db_id,
                    EmploiSchema::CREATED_AT                 => now(),
                    EmploiSchema::UPDATED_AT                 => now(),
                ]);
            }
        }

        $totalEmplois = DB::table(EmploiSchema::FULL_TABLE)->count();
        $this->command->info("✅ {$totalEmplois} définitions d'emplois créées pour " . $cinemas->count() . ' cinémas');
    }
}
