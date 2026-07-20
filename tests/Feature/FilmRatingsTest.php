<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Clean up MongoDB collections
    DB::connection('mongodb')->table('films_catalogue')->truncate();
    DB::connection('mongodb')->table('avis_films')->truncate();

    // Insert test film
    DB::connection('mongodb')->table('films_catalogue')->insert([
        'film_id'          => 'test-film-1',
        'titre'            => 'Film Test',
        'description'      => 'Description du film test',
        'duree'            => 120,
        'genre'            => 'action',
        'classification'   => 'Tous publics',
        'date_sortie'      => now()->subMonths(2),
        'statut_diffusion' => 'en_diffusion',
        'note_moyenne'     => 4.2,
        'nombre_avis'      => 5,
        'affiche_url'      => 'https://example.com/poster.jpg',
    ]);

    // Insert test ratings
    DB::connection('mongodb')->table('avis_films')->insert([
        [
            'film_id'           => 'test-film-1',
            'note'              => 5,
            'commentaire'       => 'Excellent film !',
            'nom_utilisateur'   => 'Jean Dupont',
            'email'             => 'jean@example.com',
            'statut'            => 'approuve',
            'date_creation'     => now()->subDays(3),
            'date_modification' => now()->subDays(3),
        ],
        [
            'film_id'           => 'test-film-1',
            'note'              => 4,
            'commentaire'       => 'Très bon divertissement',
            'nom_utilisateur'   => 'Marie Martin',
            'email'             => 'marie@example.com',
            'statut'            => 'approuve',
            'date_creation'     => now()->subDays(2),
            'date_modification' => now()->subDays(2),
        ],
        [
            'film_id'           => 'test-film-1',
            'note'              => 3,
            'commentaire'       => 'Pas mal mais sans plus',
            'nom_utilisateur'   => 'Pierre Durand',
            'email'             => 'pierre@example.com',
            'statut'            => 'en_attente', // This one should not appear
            'date_creation'     => now()->subDays(1),
            'date_modification' => now()->subDays(1),
        ],
    ]);
});

it('can display film ratings page', function () {
    $response = $this->get('/films/test-film-1/avis');

    $response->assertStatus(200);
    $response->assertSee('Film Test');
    $response->assertSee('Avis spectateurs');
    $response->assertSee('Donnez votre avis');
    $response->assertSee('Jean Dupont');
    $response->assertSee('Marie Martin');
    $response->assertDontSee('Pierre Durand'); // En attente should not be visible
});

it('can submit a new rating', function () {
    $response = $this->post('/films/test-film-1/avis', [
        'note'            => 5,
        'commentaire'     => 'Fantastique !',
        'nom_utilisateur' => 'Nouveau Spectateur',
        'email'           => 'nouveau@example.com',
    ]);

    $response->assertRedirect('/films/test-film-1/avis');
    $response->assertSessionHas('success');

    // Check if rating was inserted in MongoDB
    $rating = DB::connection('mongodb')->table('avis_films')
        ->where('nom_utilisateur', 'Nouveau Spectateur')
        ->first();

    expect($rating)->not->toBeNull();
    expect($rating->note)->toBe(5);
    expect($rating->statut)->toBe('en_attente');
});

it('validates rating form correctly', function () {
    $response = $this->post('/films/test-film-1/avis', [
        'note'            => 6, // Invalid note > 5
        'commentaire'     => str_repeat('a', 1001), // Too long
        'nom_utilisateur' => '', // Required
        'email'           => 'invalid-email', // Invalid email
    ]);

    $response->assertSessionHasErrors(['note', 'commentaire', 'nom_utilisateur', 'email']);
});

it('returns 404 for non-existent film ratings', function () {
    $response = $this->get('/films/non-existent-film/avis');
    $response->assertStatus(404);
});

it('calculates rating statistics correctly', function () {
    $response = $this->get('/films/test-film-1/avis');

    $response->assertStatus(200);
    // Should show average of approved ratings (5 + 4) / 2 = 4.5
    $response->assertSee('4.5');
    // Should show count of approved ratings only (2, not 3)
    $response->assertSee('Avis spectateurs (2)');
});
