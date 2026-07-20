<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Application\Bus\CommandBus;
use Illuminate\Support\Facades\File;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Application\Salle\Commands\CreateSalle\CreateSalleCommand;

class ImportSallesFromJson extends Command
{
    protected $signature = 'salles:import {--file=cinemas-data.json : The JSON file to import from resources/data/}';

    protected $description = 'Import salles from JSON file for existing cinemas';

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

        $this->info("🏛️  Starting salles import from {$filename}...");

        $json = File::get($filepath);
        $data = json_decode($json, true);

        if (!isset($data['cinemas']) || !is_array($data['cinemas'])) {
            $this->error("Invalid JSON structure. Expected 'cinemas' array.");

            return 1;
        }

        $successCount = 0;
        $errorCount   = 0;

        foreach ($data['cinemas'] as $cinemaData) {
            // Trouver le cinéma existant
            $cinema = Cinema::where('nom', $cinemaData['nom'])
                ->where('pays', $cinemaData['pays'])
                ->first();

            if (!$cinema) {
                $this->warn("Cinéma '{$cinemaData['nom']}' non trouvé, ignoré");

                continue;
            }

            if (!isset($cinemaData['salles']) || !is_array($cinemaData['salles'])) {
                $this->warn("Pas de salles pour '{$cinemaData['nom']}'");

                continue;
            }

            $this->info("📽️  Import salles pour {$cinemaData['nom']}...");

            foreach ($cinemaData['salles'] as $salleData) {
                try {
                    // Utiliser exactement le même mapping que le formulaire web
                    $salleCommand = new CreateSalleCommand(
                        cinemaUuid: $cinema->uuid->value,
                        nom: $salleData['nom'],
                        capaciteTotale: (int) $salleData['capacite_totale'],
                        nombreRangees: (int) $salleData['nombre_rangees'],
                        placesParRangee: (int) $salleData['places_par_rangee'],
                        placesStandard: (int) $salleData['places_standard'],
                        placesPmr: (int) $salleData['places_pmr'],
                        qualiteProjection: $this->mapQualiteProjection($salleData['qualite_projection'] ?? []),
                        qualiteSonore: $this->mapQualiteSonore($salleData['qualite_sonore'] ?? []),
                        climatisation: $salleData['climatisation'] ?? true,
                        accessibilitePmr: $salleData['accessibilite_pmr'] ?? true,
                        planSalle: $salleData['plan_salle'] ?? null,
                        statut: StatutSalle::ACTIVE,
                    );

                    $result = $this->commandBus->dispatch($salleCommand);

                    if ($result->isSuccess()) {
                        $this->info("  ✅ Salle créée: {$salleData['nom']}");
                        $successCount++;
                    } else {
                        $this->warn("  ⚠️  Erreur salle {$salleData['nom']}: " . $result->getErrorMessage());
                        $errorCount++;
                    }

                } catch (Exception $e) {
                    $this->warn("  ❌ Exception salle {$salleData['nom']}: " . $e->getMessage());
                    $errorCount++;
                }
            }
        }

        $this->newLine();
        $this->info('🏛️ Import des salles terminé!');
        $this->info("✅ Salles importées avec succès: {$successCount}");

        if ($errorCount > 0) {
            $this->warn("⚠️  Salles avec erreurs: {$errorCount}");
        }

        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Convertit un array de strings en array d'enums QualiteProjection
     *
     * @param array<string> $qualites
     * @return array<QualiteProjection>
     */
    private function mapQualiteProjection(array $qualites): array
    {
        if (empty($qualites)) {
            return [];
        }

        return array_map(
            fn (string $qualite) => QualiteProjection::from($qualite),
            $qualites
        );
    }

    /**
     * Convertit un array de strings en array d'enums QualiteSonore
     *
     * @param array<string> $qualites
     * @return array<QualiteSonore>
     */
    private function mapQualiteSonore(array $qualites): array
    {
        if (empty($qualites)) {
            return [];
        }

        return array_map(
            fn (string $qualite) => QualiteSonore::from($qualite),
            $qualites
        );
    }
}
