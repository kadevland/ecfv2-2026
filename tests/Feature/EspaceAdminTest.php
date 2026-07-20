<?php

declare(strict_types=1);

use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Models\Cinema\Seance;
use App\Infrastructure\Database\Models\Reservation\Reservation;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Créer utilisateur administrateur
    $this->admin = User::factory()->create([
        'email'    => 'admin@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'administrateur',
    ]);

    // Créer utilisateur employé
    $this->employee = User::factory()->create([
        'email'    => 'employee@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'employe',
    ]);

    // Créer utilisateur client
    $this->user = User::factory()->create([
        'email'    => 'user@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'client',
    ]);

    // Créer données de test
    $this->cinema = Cinema::factory()->create([
        'nom'   => 'Cinéma Test Admin',
        'actif' => true,
    ]);

    $this->salle = Salle::factory()->create([
        'cinema_uuid' => $this->cinema->uuid,
        'nom'         => 'Salle Admin Test',
        'capacite'    => 150,
    ]);

    $this->film = Film::factory()->create([
        'titre'          => 'Film Admin Test',
        'duree_minutes'  => 135,
        'classification' => 'TOUS_PUBLICS',
        'genres'         => ['drame', 'romance'],
    ]);

    $this->seance = Seance::factory()->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->addDay(),
        'tarif_normal'     => 15.00,
        'tarif_reduit'     => 12.00,
    ]);
});

// === TESTS D'ACCÈS ET AUTHENTIFICATION ===

it('redirects unauthenticated users from admin dashboard', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});

it('denies access to non-admin users', function () {
    $this->actingAs($this->user);

    $response = $this->get('/admin/dashboard');

    $response->assertStatus(403);
});

it('allows admin access to dashboard', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Dashboard Administrateur');
});

// === TESTS GESTION CINÉMAS ===

it('displays cinemas list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/cinemas');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Cinémas');
    $response->assertSee($this->cinema->nom);
});

it('displays cinema creation form', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/cinemas/create');

    $response->assertSuccessful();
    $response->assertSee('Créer un Cinéma');
    $response->assertSee('Nom du cinéma');
});

it('can create a new cinema', function () {
    $this->actingAs($this->admin);

    $cinemaData = [
        'nom'         => 'Nouveau Cinéma Test',
        'adresse'     => '123 Rue Test',
        'ville'       => 'Paris',
        'code_postal' => '75001',
        'telephone'   => '0123456789',
        'email'       => 'contact@cinema-test.fr',
        'actif'       => true,
    ];

    $response = $this->post('/admin/cinemas', $cinemaData);

    $response->assertRedirect('/admin/cinemas');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('cinemas', [
        'nom'   => 'Nouveau Cinéma Test',
        'ville' => 'Paris',
        'actif' => true,
    ]);
});

it('validates cinema creation form', function () {
    $this->actingAs($this->admin);

    $response = $this->post('/admin/cinemas', [
        'nom'     => '', // Requis
        'adresse' => '',
        'ville'   => '',
    ]);

    $response->assertSessionHasErrors(['nom', 'adresse', 'ville']);
});

it('can edit cinema details', function () {
    $this->actingAs($this->admin);

    $response = $this->get("/admin/cinemas/{$this->cinema->uuid}/edit");

    $response->assertSuccessful();
    $response->assertSee('Modifier le Cinéma');
    $response->assertSee($this->cinema->nom);
});

it('can update cinema information', function () {
    $this->actingAs($this->admin);

    $updateData = [
        'nom'         => 'Cinéma Mis à Jour',
        'adresse'     => $this->cinema->adresse,
        'ville'       => $this->cinema->ville,
        'code_postal' => $this->cinema->code_postal,
        'telephone'   => $this->cinema->telephone,
        'email'       => $this->cinema->email,
        'actif'       => true,
    ];

    $response = $this->put("/admin/cinemas/{$this->cinema->uuid}", $updateData);

    $response->assertRedirect("/admin/cinemas/{$this->cinema->uuid}");

    $this->assertDatabaseHas('cinemas', [
        'uuid' => $this->cinema->uuid,
        'nom'  => 'Cinéma Mis à Jour',
    ]);
});

it('can toggle cinema status', function () {
    $this->actingAs($this->admin);

    $response = $this->post("/admin/cinemas/{$this->cinema->uuid}/toggle-status");

    $response->assertRedirect("/admin/cinemas/{$this->cinema->uuid}");

    $this->assertDatabaseHas('cinemas', [
        'uuid'  => $this->cinema->uuid,
        'actif' => false, // Inversé
    ]);
});

// === TESTS GESTION FILMS ===

it('displays films list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/films');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Films');
    $response->assertSee($this->film->titre);
});

it('displays film creation form', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/films/create');

    $response->assertSuccessful();
    $response->assertSee('Créer un Film');
    $response->assertSee('Titre du film');
});

it('can create a new film', function () {
    $this->actingAs($this->admin);

    $filmData = [
        'titre'          => 'Nouveau Film Admin',
        'synopsis'       => 'Synopsis du film admin test',
        'duree_minutes'  => 120,
        'classification' => 'TOUS_PUBLICS',
        'genres'         => ['action', 'aventure'],
        'date_sortie'    => '2025-01-15',
        'realisateur'    => 'Réalisateur Test',
        'acteurs'        => ['Acteur 1', 'Acteur 2'],
    ];

    $response = $this->post('/admin/films', $filmData);

    $response->assertRedirect('/admin/films');

    $this->assertDatabaseHas('films', [
        'titre'         => 'Nouveau Film Admin',
        'duree_minutes' => 120,
    ]);
});

it('can update film information', function () {
    $this->actingAs($this->admin);

    $updateData = [
        'titre'          => 'Film Modifié Admin',
        'synopsis'       => $this->film->synopsis,
        'duree_minutes'  => 140,
        'classification' => $this->film->classification,
        'genres'         => $this->film->genres,
        'date_sortie'    => $this->film->date_sortie,
        'realisateur'    => $this->film->realisateur,
    ];

    $response = $this->put("/admin/films/{$this->film->uuid}", $updateData);

    $response->assertRedirect("/admin/films/{$this->film->uuid}");

    $this->assertDatabaseHas('films', [
        'uuid'          => $this->film->uuid,
        'titre'         => 'Film Modifié Admin',
        'duree_minutes' => 140,
    ]);
});

// === TESTS GESTION SALLES ===

it('displays salles list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/salles');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Salles');
    $response->assertSee($this->salle->nom);
});

it('can create a new salle', function () {
    $this->actingAs($this->admin);

    $salleData = [
        'nom'               => 'Nouvelle Salle Test',
        'cinema_uuid'       => $this->cinema->uuid,
        'capacite'          => 200,
        'type_ecran'        => '2D',
        'accessibilite_pmr' => true,
    ];

    $response = $this->post('/admin/salles', $salleData);

    $response->assertRedirect('/admin/salles');

    $this->assertDatabaseHas('salles', [
        'nom'         => 'Nouvelle Salle Test',
        'capacite'    => 200,
        'cinema_uuid' => $this->cinema->uuid,
    ]);
});

// === TESTS GESTION SÉANCES ===

it('displays seances list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/seances');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Séances');
});

it('can create a new seance', function () {
    $this->actingAs($this->admin);

    $seanceData = [
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->addDays(2)->format('Y-m-d H:i'),
        'tarif_normal'     => 13.50,
        'tarif_reduit'     => 10.50,
    ];

    $response = $this->post('/admin/seances', $seanceData);

    $response->assertRedirect('/admin/seances');

    $this->assertDatabaseHas('seances', [
        'film_uuid'    => $this->film->uuid,
        'salle_uuid'   => $this->salle->uuid,
        'tarif_normal' => 13.50,
    ]);
});

// === TESTS GESTION RÉSERVATIONS ===

it('displays reservations list page', function () {
    $this->actingAs($this->admin);

    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'ADMIN-TEST-001',
    ]);

    $response = $this->get('/admin/reservations');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Réservations');
    $response->assertSee('ADMIN-TEST-001');
});

it('can view reservation details', function () {
    $this->actingAs($this->admin);

    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'ADMIN-TEST-002',
        'total'              => 25.50,
    ]);

    $response = $this->get("/admin/reservations/{$reservation->uuid}");

    $response->assertSuccessful();
    $response->assertSee('Détails de la Réservation');
    $response->assertSee('ADMIN-TEST-002');
    $response->assertSee('25,50 €');
});

// === TESTS GESTION UTILISATEURS ===

it('displays clients list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/users/clients');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Clients');
    $response->assertSee($this->user->email);
});

it('displays employees list page', function () {
    $this->actingAs($this->admin);

    $response = $this->get('/admin/users/employees');

    $response->assertSuccessful();
    $response->assertSee('Gestion des Employés');
    $response->assertSee($this->employee->email);
});

it('can view client details', function () {
    $this->actingAs($this->admin);

    $response = $this->get("/admin/users/clients/{$this->user->uuid}");

    $response->assertSuccessful();
    $response->assertSee('Profil Client');
    $response->assertSee($this->user->email);
});

it('can edit employee information', function () {
    $this->actingAs($this->admin);

    $response = $this->get("/admin/users/employees/{$this->employee->uuid}/edit");

    $response->assertSuccessful();
    $response->assertSee('Modifier l\'Employé');
    $response->assertSee($this->employee->email);
});

// === TESTS PERMISSIONS ET SÉCURITÉ ===

it('employee cannot access admin routes', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/admin/cinemas/create');

    $response->assertStatus(403);
});

it('client cannot access admin routes', function () {
    $this->actingAs($this->user);

    $response = $this->get('/admin/films');

    $response->assertStatus(403);
});

it('validates admin form submissions', function () {
    $this->actingAs($this->admin);

    $response = $this->post('/admin/films', [
        'titre'         => '', // Requis
        'duree_minutes' => 'invalid', // Doit être numérique
    ]);

    $response->assertSessionHasErrors(['titre', 'duree_minutes']);
});

// === TESTS STATISTIQUES DASHBOARD ===

it('displays admin dashboard with statistics', function () {
    $this->actingAs($this->admin);

    // Créer quelques réservations pour les stats
    Reservation::factory(3)->create([
        'seance_uuid' => $this->seance->uuid,
        'statut'      => 'CONFIRMEE',
    ]);

    $response = $this->get('/admin/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Statistiques');
    $response->assertSee('Total Cinémas');
    $response->assertSee('Total Films');
    $response->assertSee('Réservations du mois');
});
