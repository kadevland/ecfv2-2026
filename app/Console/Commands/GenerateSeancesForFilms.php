<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Domain\Enums\StatutSeance;
use Illuminate\Support\Facades\DB;
use App\Application\Bus\CommandBus;
use App\Domain\Cinema\Enums\VersionFilmEnum;
use App\Application\Seance\Commands\CreateSeance\CreateSeanceCommand;

class GenerateSeancesForFilms extends Command
{
    protected $signature = 'seances:generate
                            {--days=30 : Nombre de jours à programmer}
                            {--seances-per-day=5 : Séances par jour par film}';

    protected $description = 'Génère des séances pour les films';

    public function __construct (
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    public function handle () : int
    {
        $this->info('🎬 Génération des séances pour les films...');

        $days          = (int) $this->option('days');
        $seancesPerDay = (int) $this->option('seances-per-day');

        // Récupérer tous les films 2026
        $films = DB::table('cinema.films')->get();

        if ($films->isEmpty()) {
            $this->warn('Aucun film trouvé.');

            return 1;
        }

        $this->info("📽️ {$films->count()} films trouvés");

        // Récupérer toutes les salles actives
        $salles = DB::table('cinema.salles')
            ->where('statut', 'ACTIVE')
            ->get();

        if ($salles->isEmpty()) {
            $this->error('Aucune salle active trouvée.');

            return 1;
        }

        $this->info("🏛️ {$salles->count()} salles disponibles");

        $totalCreated = 0;
        $totalErrors  = 0;

        // Heures de séance fixes et simples
        $heuresPossibles = [
            '14:00',
            '17:30',
            '21:00',
        ];

        foreach ($films as $film) {
            $this->info("\n📽️ Programmation pour: {$film->titre}");

            // Commencer maintenant (ignorer les vraies dates de sortie 2026)
            $dateDebut = Carbon::now();

            $filmCreatedCount = 0;

            // Pour chaque jour
            for ($day = 0; $day < $days; $day++) {
                $date = $dateDebut->copy()
                    ->addDays($day);

                // Sélectionner aléatoirement des salles
                $sallesAleatoires = $salles->random(min(3, $salles->count()));

                foreach ($sallesAleatoires as $salle) {
                    // Sélectionner aléatoirement des heures
                    $nbHeures = min($seancesPerDay, count($heuresPossibles));

                    // Si on demande plusieurs heures, random retourne une Collection
                    // Si on demande 1 heure, random retourne une string
                    if ($nbHeures > 1) {
                        $heuresSelectionnees = collect($heuresPossibles)->random($nbHeures)
                            ->toArray();
                    } else {
                        $heuresSelectionnees = [collect($heuresPossibles)->random()];
                    }

                    foreach ($heuresSelectionnees as $heure) {
                        $dateHeureDebut = $date->copy()
                            ->setTimeFromTimeString($heure);

                        // Skip conflit check pour génération rapide

                        try {
                            // Calculer l'heure de fin
                            $dateHeureFin = $dateHeureDebut->copy()
                                ->addMinutes($film->duree_minutes + 30); // 30 min de pub/nettoyage

                            // Créer la séance via CQRS avec les bons paramètres
                            $command = new CreateSeanceCommand(
                                filmUuid: $film->uuid,
                                salleUuid: $salle->uuid,
                                dateHeureDebut: $dateHeureDebut->format('Y-m-d H:i:s'),
                                dateHeureFin: $dateHeureFin->format('Y-m-d H:i:s'),
                                version: VersionFilmEnum::VF->value,
                                tarifsBase: [
                                    'normal' => $this->determinePrix($film->classification),
                                    'reduit' => $this->determinePrix($film->classification) - 2.00,
                                    'enfant' => $this->determinePrix($film->classification) - 3.00,
                                ],
                                tauxTva: 10.0,
                                dureeAdditionnelle: 30,
                                devise: 'EUR',
                                placementLibre: false,
                                statut: StatutSeance::PROGRAMMEE->value
                            );

                            $result = $this->commandBus->dispatch($command);

                            if ($result->isSuccess()) {
                                $filmCreatedCount++;
                                $totalCreated++;
                            } else {
                                $totalErrors++;
                            }

                        } catch (Exception $e) {
                            $totalErrors++;
                            $this->error('Erreur: ' . $e->getMessage());
                        }
                    }
                }
            }

            $this->info("   ✅ {$filmCreatedCount} séances créées pour {$film->titre}");
        }

        $this->newLine();
        $this->info('📊 Génération terminée:');
        $this->info("✅ Séances créées: {$totalCreated}");

        if ($totalErrors > 0) {
            $this->warn("⚠️ Erreurs: {$totalErrors}");
        }

        // Remettre la synchronisation MongoDB (on va corriger les listeners)
        if ($totalCreated > 0) {
            $this->info("\n🔄 Synchronisation MongoDB...");
            $this->call('seances:sync-to-mongodb');
        }

        return 0;
    }

    private function determinePrix (string $classification) : float
    {
        return match ($classification) {
            'MOINS_16', 'MOINS_18' => 13.50,
            'MOINS_12'             => 11.50,
            'AVERTISSEMENT'        => 10.50,
            default                => 9.50,
        };
    }

    private function determineVersion () : string
    {
        $versions = [
            VersionFilmEnum::VF->value,
            VersionFilmEnum::VF->value,
            VersionFilmEnum::VF->value,
            VersionFilmEnum::VOSTFR->value,
            VersionFilmEnum::VO->value,
        ];

        return $versions[array_rand($versions)];
    }
}
