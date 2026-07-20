<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Ramsey\Uuid\Uuid;
use Illuminate\Console\Command;
use App\Application\Bus\CommandBus;
use Illuminate\Support\Facades\File;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use App\Application\Salle\Commands\CreateSalle\CreateSalleCommand;
use App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommand;

class ImportCinemasFromJson extends Command
{
    protected $signature = 'cinemas:import {--file=cinemas-data.json : The JSON file to import from resources/data/}';

    protected $description = 'Import cinemas and their salles from JSON file using CQRS commands';

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

        $this->info("🏛️  Starting cinema import from {$filename}...");

        $json = File::get($filepath);
        $data = json_decode($json, true);

        if (!isset($data['cinemas']) || !is_array($data['cinemas'])) {
            $this->error("Invalid JSON structure. Expected 'cinemas' array.");

            return 1;
        }

        $successCount = 0;
        $errorCount   = 0;

        $this->withProgressBar($data['cinemas'], function (array $cinemaData) use (&$successCount, &$errorCount) {
            try {

                // Générer un UUID v7 pour le cinema
                $cinemaUuid = Uuid::uuid7()->toString();
                $cinemaId   = CinemaId::fromString($cinemaUuid);

                // Créer horaires d'ouverture standards pour un cinéma
                $horairesStandard = HorairesOuverture::standard();

                // Créer la commande CQRS pour le cinema
                $cinemaCommand = new CreateCinemaCommand(
                    nom: $cinemaData['nom'],
                    pays: $cinemaData['pays'],
                    rue: $cinemaData['rue'],
                    ville: $cinemaData['ville'],
                    codePostal: $cinemaData['code_postal'],
                    telephone: $cinemaData['telephone'] ?? null,
                    email: $cinemaData['email'] ?? null,
                    description: $cinemaData['description'] ?? null,
                    estActif: $cinemaData['est_actif'] ?? true,
                    latitude: isset($cinemaData['latitude']) ? (float) $cinemaData['latitude'] : null,
                    longitude: isset($cinemaData['longitude']) ? (float) $cinemaData['longitude'] : null,
                    horaires: $horairesStandard,
                );

                // Dispatch cinema via CommandBus
                $result = $this->commandBus->dispatch($cinemaCommand);

                if ($result->isSuccess()) {
                    $this->newLine();
                    $this->info("✅ Cinema créé: {$cinemaData['nom']}");

                    // Créer les salles si présentes
                    if (isset($cinemaData['salles']) && is_array($cinemaData['salles']) && false) {
                        //dump($cinemaData['salles']);
                        foreach ($cinemaData['salles'] as $salleData) {
                            $this->createSalle($cinemaId->value, $salleData);
                        }
                    }

                    $successCount++;
                } else {
                    $errorCount++;
                    $this->newLine();
                    $this->error("❌ Erreur pour {$cinemaData['nom']}: " . $result->getErrorMessage());
                }

            } catch (Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Exception pour {$cinemaData['nom']}: " . $e->getMessage());
            }
        });

        $this->newLine(2);
        $this->info('🏛️ Import terminé!');
        $this->info("✅ Cinemas importés avec succès: {$successCount}");

        if ($errorCount > 0) {
            $this->warn("⚠️  Cinemas avec erreurs: {$errorCount}");
        }

        return $errorCount > 0 ? 1 : 0;
    }

    private function createSalle(string $cinemaUuid, array $salleData): void
    {
        try {
            $salleCommand = new CreateSalleCommand(
                cinemaUuid: $cinemaUuid,
                nom: $salleData['nom'],
                capaciteTotale: (int) $salleData['capacite_totale'],
                nombreRangees: (int) $salleData['nombre_rangees'],
                placesParRangee: (int) $salleData['places_par_rangee'],
                placesStandard: (int) $salleData['places_standard'],
                placesPmr: (int) $salleData['places_pmr'],
                qualiteProjection: $salleData['qualite_projection'] ?? [],
                qualiteSonore: $salleData['qualite_sonore'] ?? [],
                accessibilitePmr: $salleData['accessibilite_pmr'] ?? false,
                climatisation: $salleData['climatisation'] ?? true,
                planSalle: $salleData['plan_salle'] ?? null,
            );

            $result = $this->commandBus->dispatch($salleCommand);

            if ($result->isSuccess()) {
                $this->info("  ✅ Salle créée: {$salleData['nom']}");
            } else {
                $this->warn("  ⚠️  Erreur salle {$salleData['nom']}: " . $result->getErrorMessage());
            }

        } catch (Exception $e) {
            $this->warn("  ❌ Exception salle {$salleData['nom']}: " . $e->getMessage());
        }
    }
}
