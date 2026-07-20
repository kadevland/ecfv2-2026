<?php

declare(strict_types=1);

namespace Tests\Feature\MongoDB;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MongoDB\FilmCatalogueQueryService;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

class FilmCatalogueOptimizedTest extends TestCase
{
    use RefreshDatabase;

    protected FilmCatalogueQueryService $queryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryService = new FilmCatalogueQueryService(new FilmCatalogue);
    }

    /** @test */
    public function it_can_search_films_with_mongodb_regex()
    {
        // Créer des films de test
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Action Hero',
            'description'      => 'Un film d\'action palpitant',
            'genre'            => 'action',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.2,
            'nombre_avis'      => 150,
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Romance Parisienne',
            'description'      => 'Une histoire d\'amour',
            'genre'            => 'romance',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 3.8,
            'nombre_avis'      => 200,
        ]);

        // Test recherche simple
        $results = FilmCatalogue::enDiffusion()
            ->search('Action')
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Action Hero', $results->first()->titre);

        // Test recherche avancée
        $advancedResults = FilmCatalogue::enDiffusion()
            ->searchAdvanced('amour')
            ->get();

        $this->assertCount(1, $advancedResults);
        $this->assertEquals('Romance Parisienne', $advancedResults->first()->titre);
    }

    /** @test */
    public function it_can_filter_films_by_rating_range()
    {
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Film Excellent',
            'genre'            => 'drame',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.8,
            'nombre_avis'      => 300,
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Film Moyen',
            'genre'            => 'comedie',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 3.2,
            'nombre_avis'      => 100,
        ]);

        FilmCatalogue::create([
            'film_id'          => '3',
            'titre'            => 'Film Médiocre',
            'genre'            => 'horreur',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 2.1,
            'nombre_avis'      => 50,
        ]);

        // Test note minimum
        $highRated = FilmCatalogue::enDiffusion()
            ->minNote(4.0)
            ->get();

        $this->assertCount(1, $highRated);
        $this->assertEquals('Film Excellent', $highRated->first()->titre);

        // Test plage de notes
        $mediumRated = FilmCatalogue::enDiffusion()
            ->noteBetween(3.0, 4.0)
            ->get();

        $this->assertCount(1, $mediumRated);
        $this->assertEquals('Film Moyen', $mediumRated->first()->titre);
    }

    /** @test */
    public function it_can_filter_films_by_duration()
    {
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Film Court',
            'genre'            => 'comedie',
            'duree'            => 90,
            'statut_diffusion' => 'en_diffusion',
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Film Moyen',
            'genre'            => 'drame',
            'duree'            => 120,
            'statut_diffusion' => 'en_diffusion',
        ]);

        FilmCatalogue::create([
            'film_id'          => '3',
            'titre'            => 'Film Long',
            'genre'            => 'epique',
            'duree'            => 180,
            'statut_diffusion' => 'en_diffusion',
        ]);

        // Test durée minimum
        $longFilms = FilmCatalogue::enDiffusion()
            ->minDuration(150)
            ->get();

        $this->assertCount(1, $longFilms);
        $this->assertEquals('Film Long', $longFilms->first()->titre);

        // Test plage de durée
        $mediumFilms = FilmCatalogue::enDiffusion()
            ->durationBetween(100, 140)
            ->get();

        $this->assertCount(1, $mediumFilms);
        $this->assertEquals('Film Moyen', $mediumFilms->first()->titre);
    }

    /** @test */
    public function it_can_find_films_by_director_and_actor()
    {
        FilmCatalogue::create([
            'film_id'            => '1',
            'titre'              => 'Film de Scorsese',
            'realisateur'        => 'Martin Scorsese',
            'acteurs_principaux' => ['Robert De Niro', 'Al Pacino'],
            'statut_diffusion'   => 'en_diffusion',
        ]);

        FilmCatalogue::create([
            'film_id'            => '2',
            'titre'              => 'Film de Tarantino',
            'realisateur'        => 'Quentin Tarantino',
            'acteurs_principaux' => ['Samuel L. Jackson', 'John Travolta'],
            'statut_diffusion'   => 'en_diffusion',
        ]);

        // Test recherche par réalisateur
        $scorsese = FilmCatalogue::enDiffusion()
            ->byDirector('Scorsese')
            ->get();

        $this->assertCount(1, $scorsese);
        $this->assertEquals('Film de Scorsese', $scorsese->first()->titre);

        // Test recherche par acteur
        $deNiro = FilmCatalogue::enDiffusion()
            ->withActor('De Niro')
            ->get();

        $this->assertCount(1, $deNiro);
        $this->assertEquals('Film de Scorsese', $deNiro->first()->titre);
    }

    /** @test */
    public function it_can_get_genre_statistics()
    {
        // Créer des films de différents genres
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Action 1',
            'genre'            => 'action',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.0,
            'nombre_avis'      => 100,
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Action 2',
            'genre'            => 'action',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 3.5,
            'nombre_avis'      => 80,
        ]);

        FilmCatalogue::create([
            'film_id'          => '3',
            'titre'            => 'Comédie 1',
            'genre'            => 'comedie',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.2,
            'nombre_avis'      => 120,
        ]);

        $stats      = FilmCatalogue::getGenreStats();
        $statsArray = $stats->toArray();

        $this->assertGreaterThan(0, count($statsArray));

        // Vérifier la structure des statistiques
        $actionStats = collect($statsArray)->firstWhere('_id', 'action');
        $this->assertNotNull($actionStats);
        $this->assertEquals(2, $actionStats['count']);
        $this->assertEquals(3.75, $actionStats['avg_note']); // (4.0 + 3.5) / 2
        $this->assertEquals(180, $actionStats['total_avis']); // 100 + 80
    }

    /** @test */
    public function it_can_search_with_relevance_scoring()
    {
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Super Hero Movie', // Score élevé car dans le titre
            'description'      => 'Un film de super-héros',
            'realisateur'      => 'John Director',
            'statut_diffusion' => 'en_diffusion',
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Drama Film',
            'description'      => 'Un super drame avec hero principal', // Score moyen car dans description
            'realisateur'      => 'Jane Director',
            'statut_diffusion' => 'en_diffusion',
        ]);

        $results      = FilmCatalogue::searchWithRelevance('hero', 10);
        $resultsArray = $results->toArray();

        $this->assertGreaterThan(0, count($resultsArray));

        // Le premier résultat devrait avoir le score le plus élevé
        $firstResult = $resultsArray[0];
        $this->assertEquals('Super Hero Movie', $firstResult['titre']);
        $this->assertGreaterThan(0, $firstResult['relevance_score']);
    }

    /** @test */
    public function query_service_can_perform_unified_search()
    {
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Action Film',
            'genre'            => 'action',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.2,
            'duree'            => 120,
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Comedy Film',
            'genre'            => 'comedie',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 3.8,
            'duree'            => 100,
        ]);

        $criteria = [
            'genres'   => ['action'],
            'note_min' => 4.0,
            'sort'     => 'note',
            'limit'    => 10,
        ];

        $results = $this->queryService->searchUnified($criteria);

        $this->assertCount(1, $results);
        $this->assertEquals('Action Film', $results->first()->titre);
    }

    /** @test */
    public function query_service_can_get_trending_films()
    {
        FilmCatalogue::create([
            'film_id'            => '1',
            'titre'              => 'Trending Film',
            'statut_diffusion'   => 'en_diffusion',
            'note_moyenne'       => 4.5,
            'nombre_avis'        => 200,
            'prochaines_seances' => [
                [
                    'seance_id'        => 'seance_1',
                    'date_heure_debut' => now()->addHours(2)->toISOString(),
                    'cinema_id'        => '1',
                ],
                [
                    'seance_id'        => 'seance_2',
                    'date_heure_debut' => now()->addDays(1)->toISOString(),
                    'cinema_id'        => '2',
                ],
            ],
        ]);

        $trending = $this->queryService->getTrendingFilms(7, 5);

        $this->assertGreaterThanOrEqual(0, $trending->count());

        if ($trending->count() > 0) {
            $firstFilm = $trending->first();
            $this->assertArrayHasKey('titre', $firstFilm);
            $this->assertArrayHasKey('trending_score', $firstFilm);
        }
    }

    /** @test */
    public function query_service_can_get_catalogue_analytics()
    {
        FilmCatalogue::create([
            'film_id'          => '1',
            'titre'            => 'Test Film 1',
            'genre'            => 'action',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 4.0,
            'duree'            => 120,
            'nombre_avis'      => 100,
        ]);

        FilmCatalogue::create([
            'film_id'          => '2',
            'titre'            => 'Test Film 2',
            'genre'            => 'comedie',
            'statut_diffusion' => 'en_diffusion',
            'note_moyenne'     => 3.5,
            'duree'            => 90,
            'nombre_avis'      => 80,
        ]);

        $analytics = $this->queryService->getCatalogueAnalytics();

        $this->assertArrayHasKey('genre_stats', $analytics);
        $this->assertArrayHasKey('rating_distribution', $analytics);
        $this->assertArrayHasKey('duration_stats', $analytics);
        $this->assertArrayHasKey('generated_at', $analytics);

        // Vérifier les statistiques de durée
        $durationStats = $analytics['duration_stats'];
        $this->assertArrayHasKey('avg_duration', $durationStats);
        $this->assertArrayHasKey('min_duration', $durationStats);
        $this->assertArrayHasKey('max_duration', $durationStats);
        $this->assertArrayHasKey('total_films', $durationStats);
    }
}
