<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Application\Bus\CommandBus;
use Illuminate\Support\Facades\File;
use App\Application\Film\Commands\CreateFilm\CreateFilmCommand;

class ImportFilmsFromJson extends Command
{
    protected $signature = 'films:import {--file=films-data.json : The JSON file to import from resources/data/}';

    protected $description = 'Import films from JSON file using CQRS commands (triggers MongoDB sync)';

    public function __construct(
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $filename = $this->option('file');
        $filepath = resource_path("data/{$filename}");

        if (!File::exists($filepath)) {
            $this->error("File not found: {$filepath}");

            return 1;
        }

        $this->info("📽️  Starting film import from {$filename}...");

        $json = File::get($filepath);
        $data = json_decode($json, true);

        if (!isset($data['films']) || !is_array($data['films'])) {
            $this->error("Invalid JSON structure. Expected 'films' array.");

            return 1;
        }

        $successCount = 0;
        $errorCount   = 0;

        $this->withProgressBar($data['films'], function ($filmData) use (&$successCount, &$errorCount) {
            try {
                // Créer la commande CQRS avec la vraie signature
                $command = new CreateFilmCommand(
                    titre: $filmData['titre'],
                    realisateurs: $filmData['realisateurs'] ?? [],
                    genres: $filmData['genres'] ?? [],
                    dureeMinutes: $filmData['duree_minutes'],
                    classification: $filmData['classification'] ?? 'TOUS_PUBLICS',
                    dateSortie: $filmData['date_sortie'],
                    titreFr: $filmData['titre_original'] ?? null,
                    acteursPrincipaux: $filmData['acteurs_principaux'] ?? [],
                    langueOriginale: $filmData['langue_originale'] ?? null,
                    sousTitres: $filmData['sous_titres'] ?? null,
                    resume: $filmData['synopsis'] ?? null,
                    dateFinExploitation: $filmData['date_fin_exploitation'] ?? null,
                    notePresse: isset($filmData['note_critique']) ? (float) $filmData['note_critique'] : null,
                    notePublic: isset($filmData['note_public']) ? (float) $filmData['note_public'] : null,
                    afficheUrl: $filmData['affiche_url'] ?? null,
                    bandeAnnonceUrl: $filmData['bande_annonce_url'] ?? null,
                    estActif: $filmData['est_actif'] ?? true,
                );

                // Dispatch via CommandBus (déclenche les events et la synchro MongoDB)
                $result = $this->commandBus->dispatch($command);

                if ($result->isSuccess()) {
                    $successCount++;
                    $this->newLine();
                    $this->info("✅ Film créé: {$filmData['titre']}");
                } else {
                    $errorCount++;
                    $this->newLine();
                    $this->error("❌ Erreur pour {$filmData['titre']}: " . $result->getErrorMessage());
                }

            } catch (Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Exception pour {$filmData['titre']}: " . $e->getMessage());
            }
        });

        $this->newLine(2);
        $this->info('🎬 Import terminé!');
        $this->info("✅ Films importés avec succès: {$successCount}");

        if ($errorCount > 0) {
            $this->warn("⚠️  Films avec erreurs: {$errorCount}");
        }

        return $errorCount > 0 ? 1 : 0;
    }
}
