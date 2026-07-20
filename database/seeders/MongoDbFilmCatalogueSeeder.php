<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Models\Cinema\Cinema;

class MongoDbFilmCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎬 Synchronisation PostgreSQL → MongoDB...');

        // Vider la collection MongoDB d'abord
        DB::connection('mongodb')->table('films_catalogue')->truncate();

        // Récupérer tous les films PostgreSQL
        $filmsPostgres   = Film::where('est_actif', true)->get();
        $cinemasPostgres = Cinema::where('est_actif', true)->get();

        $filmsProcessed = 0;

        foreach ($filmsPostgres as $filmPg) {
            // Pour l'instant, créons des données simplifiées
            $cinemasDiffusion = [
                ['cinema_id' => '1', 'nom_cinema' => 'Pathé Bellecour', 'ville' => 'Lyon', 'prochaines_seances_count' => 3],
                ['cinema_id' => '2', 'nom_cinema' => 'UGC Part-Dieu', 'ville' => 'Lyon', 'prochaines_seances_count' => 2],
            ];

            $prochainesSeances = [
                [
                    'seance_id'          => 'seance_' . $filmPg->id . '_1',
                    'cinema_id'          => '1',
                    'nom_cinema'         => 'Pathé Bellecour',
                    'date_heure_debut'   => now()->addHours(2)->toISOString(),
                    'places_disponibles' => rand(50, 200),
                ],
                [
                    'seance_id'          => 'seance_' . $filmPg->id . '_2',
                    'cinema_id'          => '1',
                    'nom_cinema'         => 'Pathé Bellecour',
                    'date_heure_debut'   => now()->addHours(5)->toISOString(),
                    'places_disponibles' => rand(50, 200),
                ],
            ];

            // Préparer le document MongoDB
            $filmMongo = [
                'film_id'            => $filmPg->id,
                'titre'              => $filmPg->titre,
                'description'        => $filmPg->synopsis ?? 'Synopsis non disponible',
                'genre'              => $this->mapGenre($filmPg->genre ?? 'DRAMA'),
                'duree'              => $filmPg->duree_minutes ?? 120,
                'classification'     => $this->mapClassification($filmPg->classification ?? 'TOUS_PUBLICS'),
                'date_sortie'        => $filmPg->date_sortie ?? now()->subDays(30),
                'realisateur'        => implode(', ', $filmPg->realisateurs) ?: 'Réalisateur inconnu',
                'acteurs_principaux' => $filmPg->acteurs_principaux ?? 'Acteurs non disponibles',
                'affiche_url'        => $filmPg->affiche_url,
                'bande_annonce_url'  => $filmPg->bande_annonce_url,
                'note_moyenne'       => $filmPg->note_public ?? (float) rand(30, 50) / 10,
                'nombre_avis'        => rand(50, 500),
                'statut_diffusion'   => 'en_diffusion',
                'cinemas_diffusion'  => $cinemasDiffusion,
                'prochaines_seances' => $prochainesSeances,
                'created_at'         => now(),
                'updated_at'         => now(),
                'deleted_at'         => null, // Pour SoftDeletes
            ];

            // Insérer dans MongoDB
            DB::connection('mongodb')->table('films_catalogue')->insert($filmMongo);
            $filmsProcessed++;
        }

        $this->command->info("✅ Films catalogue MongoDB synchronisé avec {$filmsProcessed} films depuis PostgreSQL");
    }

    private function mapGenre(string $genre): string
    {
        $mapping = [
            'ACTION'          => 'action',
            'ADVENTURE'       => 'aventure',
            'COMEDY'          => 'comedie',
            'DRAMA'           => 'drame',
            'HORROR'          => 'horreur',
            'THRILLER'        => 'thriller',
            'SCIENCE_FICTION' => 'science-fiction',
            'FANTASY'         => 'fantastique',
            'ROMANCE'         => 'romance',
            'ANIMATION'       => 'animation',
            'DOCUMENTARY'     => 'documentaire',
            'BIOGRAPHY'       => 'biographie',
            'HISTORICAL'      => 'historique',
            'MUSICAL'         => 'musical',
            'WESTERN'         => 'western',
            'WAR'             => 'guerre',
            'CRIME'           => 'policier',
            'MYSTERY'         => 'mystere',
        ];

        return $mapping[strtoupper($genre)] ?? strtolower($genre);
    }

    private function mapClassification(string $classification): string
    {
        $mapping = [
            'TOUS_PUBLICS'  => 'tous_publics',
            'AVERTISSEMENT' => 'avertissement',
            '12_ANS'        => '12_ans',
            '16_ANS'        => '16_ans',
            '18_ANS'        => '18_ans',
        ];

        return $mapping[strtoupper($classification)] ?? strtolower($classification);
    }
}
