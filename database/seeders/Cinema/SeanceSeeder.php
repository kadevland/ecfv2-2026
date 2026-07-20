<?php

declare(strict_types=1);

namespace Database\Seeders\Cinema;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Seeder;
use App\Domain\Enums\VersionFilm;
use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Models\Cinema\Seance;

final class SeanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎬 Seedage des séances...');

        $films  = Film::all();
        $salles = Salle::all();

        if ($films->isEmpty() || $salles->isEmpty()) {
            $this->command->warn('⚠️  Aucun film ou salle trouvé. Veuillez seeder les films et salles avant.');

            return;
        }

        $seances   = [];
        $dateDebut = Carbon::now()->addDays(1); // Demain

        // Créer des séances pour les 30 prochains jours
        for ($jour = 0; $jour < 30; $jour++) {
            $dateSeance = $dateDebut->copy()->addDays($jour);

            // Organiser les séances par salle pour éviter les conflits
            foreach ($salles as $salle) {
                // 2-3 séances par salle par jour
                $nbSeancesSalle = rand(2, 3);

                // Planifier les séances de manière séquentielle pour éviter tout conflit
                $heureDebutCourante = Carbon::parse($dateSeance->format('Y-m-d') . ' 14:00:00');

                for ($i = 0; $i < $nbSeancesSalle; $i++) {
                    $film = $films->random();

                    // Calculer heure de fin (durée film + durée additionnelle)
                    $dureeAdditionnelle = rand(15, 45);
                    $dureeTotal         = $film->duree_minutes + $dureeAdditionnelle;
                    $heureFin           = $heureDebutCourante->copy()->addMinutes($dureeTotal);

                    // S'assurer qu'on ne dépasse pas 23h30
                    if ($heureFin->format('H:i') > '23:30') {
                        break;
                    }

                    $seances[] = [
                        'uuid'        => (string) Uuid::uuid7(),
                        'film_uuid'   => $film->uuid,
                        'film_db_id'  => $film->db_id,
                        'salle_uuid'  => $salle->uuid,
                        'salle_db_id' => $salle->db_id,

                        // Dates
                        'date_seance'      => $dateSeance->format('Y-m-d'),
                        'heure_debut'      => $heureDebutCourante->format('H:i:s'),
                        'heure_fin'        => $heureFin->format('H:i:s'),
                        'date_heure_debut' => $heureDebutCourante->format('Y-m-d H:i:s'),
                        'date_heure_fin'   => $heureFin->format('Y-m-d H:i:s'),

                        // Version
                        'version' => $this->getRandomVersion(),

                        // Qualités (nouvelles colonnes directes)
                        'duree_additionnelle' => $dureeAdditionnelle,
                        'qualite_projection'  => $this->getRandomQualiteProjection(),
                        'qualite_sonore'      => $this->getRandomQualiteSonore(),

                        // Prix et tarification
                        'est_3d'                => rand(0, 10) > 7, // 30% de chance d'être en 3D
                        'prix_ht_centimes'      => rand(800, 1500),
                        'devise'                => 'EUR',
                        'taux_tva_basis_points' => 2000, // 20%
                        'prix_ttc_centimes'     => function ($prix) {
                            return (int) ($prix * 1.2);
                        },

                        // Tarification complexe
                        'tarification' => $this->generateTarification(),
                        'taux_tva'     => json_encode(['pourcentage' => 20, 'basis_points' => 2000]),

                        // Places
                        'places_disponibles' => $salle->capacite ?? 120,
                        'places_reservees'   => 0,
                        'places_vendues'     => 0,
                        'placement_libre'    => rand(0, 10) > 8, // 20% de placement libre

                        // Statut
                        'statut' => $this->getRandomStatut($dateSeance),

                        // Metadata
                        'seance_speciale'          => rand(0, 10) > 8, // 20% de séances spéciales
                        'notes'                    => null,
                        'configuration_technique'  => null,
                        'metadonnees_commerciales' => null,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Préparer l'heure de début pour la prochaine séance (fin + 30 min de nettoyage)
                    $heureDebutCourante = $heureFin->copy()->addMinutes(30);
                }
            }
        }

        // Calculer prix_ttc_centimes
        foreach ($seances as &$seance) {
            if (is_callable($seance['prix_ttc_centimes'])) {
                $seance['prix_ttc_centimes'] = $seance['prix_ttc_centimes']($seance['prix_ht_centimes']);
            }
        }

        // Insérer par chunks pour éviter les problèmes de mémoire
        $chunks = array_chunk($seances, 50);
        foreach ($chunks as $chunk) {
            Seance::insert($chunk);
        }

        $this->command->info('✅ ' . count($seances) . ' séances créées pour les 30 prochains jours');
    }

    private function getRandomVersion(): string
    {
        $versions = VersionFilm::cases();

        return $versions[array_rand($versions)]->value;
    }

    private function getRandomQualiteProjection(): ?string
    {
        // 40% de chance d'avoir une qualité spéciale
        if (rand(0, 10) < 4) {
            return null; // Standard
        }

        $qualites = QualiteProjection::cases();

        return $qualites[array_rand($qualites)]->value;
    }

    private function getRandomQualiteSonore(): ?string
    {
        // 30% de chance d'avoir une qualité spéciale
        if (rand(0, 10) < 7) {
            return null; // Standard
        }

        $qualites = QualiteSonore::cases();

        return $qualites[array_rand($qualites)]->value;
    }

    private function getRandomStatut(Carbon $dateSeance): string
    {
        $now = Carbon::now();

        if ($dateSeance->isPast()) {
            return StatutSeance::TERMINEE->value;
        } elseif ($dateSeance->isToday()) {
            return rand(0, 1) ? StatutSeance::EN_COURS->value : StatutSeance::PROGRAMMEE->value;
        } else {
            // 95% programmées, 5% annulées
            return rand(0, 20) === 0 ? StatutSeance::ANNULEE->value : StatutSeance::PROGRAMMEE->value;
        }
    }

    private function generateTarification(): string
    {
        $tarifsBase = [
            'normal' => rand(1000, 1500),
            'reduit' => rand(700, 1000),
            'enfant' => rand(500, 800),
        ];

        return json_encode([
            'tarifs_base'          => $tarifsBase,
            'supplements_speciaux' => null,
            'reductions_speciales' => null,
        ]);
    }
}
