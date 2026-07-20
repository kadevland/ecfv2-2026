<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Cinema\Events\FilmUpdated;
use App\Domain\Cinema\Events\SalleUpdated;
use App\Domain\Cinema\Events\CinemaUpdated;
use App\Domain\Cinema\Events\SeanceUpdated;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Models\Cinema\Seance;

class TriggerCinemaEvents extends Command
{
    protected $signature = 'events:trigger-cinema
                            {--type=all : Type (all|cinemas|salles|films|seances)}
                            {--limit=100 : Limit per type}';

    protected $description = 'Déclenche les événements de synchronisation pour forcer la sync MongoDB';

    public function handle(): int
    {
        $type  = $this->option('type');
        $limit = (int) $this->option('limit');

        $this->info('Déclenchement des événements de synchronisation...');

        if ($type === 'all' || $type === 'cinemas') {
            $this->triggerCinemaEvents($limit);
        }

        if ($type === 'all' || $type === 'salles') {
            $this->triggerSalleEvents($limit);
        }

        if ($type === 'all' || $type === 'films') {
            $this->triggerFilmEvents($limit);
        }

        if ($type === 'all' || $type === 'seances') {
            $this->triggerSeanceEvents($limit);
        }

        $this->info('✅ Événements déclenchés avec succès!');

        return 0;
    }

    private function triggerCinemaEvents(int $limit): void
    {
        $this->info('Déclenchement événements Cinemas...');

        $cinemas = Cinema::limit($limit)->get();

        foreach ($cinemas as $cinemaModel) {
            event(CinemaUpdated::fromUuid((string) $cinemaModel->uuid));
        }

        $this->info("{$cinemas->count()} événements Cinema déclenchés");
    }

    private function triggerSalleEvents(int $limit): void
    {
        $this->info('Déclenchement événements Salles...');

        $salles = Salle::limit($limit)->get();

        foreach ($salles as $salleModel) {
            event(SalleUpdated::fromUuid((string) $salleModel->uuid));
        }

        $this->info("{$salles->count()} événements Salle déclenchés");
    }

    private function triggerFilmEvents(int $limit): void
    {
        $this->info('Déclenchement événements Films...');

        $films = Film::limit($limit)->get();

        foreach ($films as $filmModel) {
            event(FilmUpdated::fromUuid((string) $filmModel->uuid));
        }

        $this->info("{$films->count()} événements Film déclenchés");
    }

    private function triggerSeanceEvents(int $limit): void
    {
        $this->info('Déclenchement événements Séances...');

        $seances = Seance::limit($limit)->get();

        foreach ($seances as $seanceModel) {
            event(SeanceUpdated::fromUuid((string) $seanceModel->uuid));
        }

        $this->info("{$seances->count()} événements Séance déclenchés");
    }
}
