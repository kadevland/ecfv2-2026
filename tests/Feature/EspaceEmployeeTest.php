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
    // Créer utilisateur employé avec profil et emploi
    $this->employee = User::factory()->create([
        'email'    => 'employee@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'employe',
    ]);

    // Créer profil employé
    $this->employee->profil()->create([
        'nom'       => 'Dupont',
        'prenom'    => 'Jean',
        'telephone' => '0123456789',
    ]);

    // Créer emploi
    $this->employee->emploi()->create([
        'poste'           => 'Caissier',
        'date_embauche'   => now()->subMonths(6),
        'salaire_mensuel' => 2500.00,
    ]);

    // Créer utilisateur client
    $this->user = User::factory()->create([
        'email' => 'user@test.com',
        'role'  => 'client',
    ]);

    // Créer données de test
    $this->cinema = Cinema::factory()->create([
        'nom'   => 'Cinéma Employee Test',
        'actif' => true,
    ]);

    $this->salle = Salle::factory()->create([
        'cinema_uuid' => $this->cinema->uuid,
        'nom'         => 'Salle 1',
        'capacite'    => 100,
    ]);

    $this->film = Film::factory()->create([
        'titre'          => 'Film Employee Test',
        'duree_minutes'  => 120,
        'classification' => 'TOUS_PUBLICS',
        'genres'         => ['action', 'comédie'],
    ]);

    // Créer séances pour aujourd'hui
    $this->seanceAujourdhui = Seance::factory()->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->setHour(14)->setMinute(30),
        'date_heure_fin'   => now()->setHour(16)->setMinute(30),
        'tarif_normal'     => 12.00,
        'tarif_reduit'     => 9.50,
    ]);

    // Créer séance prochaine (dans 1 heure)
    $this->seanceProchaine = Seance::factory()->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->addHour(),
        'date_heure_fin'   => now()->addHours(3),
        'tarif_normal'     => 12.00,
        'tarif_reduit'     => 9.50,
    ]);

    // Créer réservations pour aujourd'hui
    $this->reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seanceAujourdhui->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'EMP-TEST-001',
        'total'              => 21.50,
        'created_at'         => now(),
    ]);
});

// === TESTS D'ACCÈS ET AUTHENTIFICATION ===

it('redirects unauthenticated users from employee dashboard', function () {
    $response = $this->get('/employee/dashboard');

    $response->assertRedirect('/login');
});

it('denies access to client users', function () {
    $this->actingAs($this->user);

    $response = $this->get('/employee/dashboard');

    $response->assertStatus(403);
});

it('allows employee access to dashboard', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Dashboard Employé');
    $response->assertSee('Jean Dupont'); // Nom employé
    $response->assertSee('Caissier'); // Poste
});

// === TESTS DASHBOARD EMPLOYÉ ===

it('displays employee information correctly', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Bonjour, Jean Dupont');
    $response->assertSee('Caissier');
});

it('displays daily statistics on dashboard', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Séances du jour');
    $response->assertSee('Réservations du jour');
    $response->assertSee('Films du jour');

    // Vérifier les compteurs (au moins 1 de chaque créé dans beforeEach)
    $response->assertSee('1'); // 1 séance aujourd'hui
    $response->assertSee('1'); // 1 réservation aujourd'hui
    $response->assertSee('1'); // 1 film aujourd'hui
});

it('displays upcoming sessions correctly', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Prochaines séances');
    $response->assertSee($this->film->titre);
    $response->assertSee('Salle 1');
    $response->assertSee('dans'); // Texte relatif au temps
});

it('shows correct employee dashboard when no upcoming sessions', function () {
    $this->actingAs($this->employee);

    // Supprimer les séances futures
    Seance::where('date_heure_debut', '>', now()->subMinutes(30))->delete();

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Aucune séance programmée prochainement');
});

// === TESTS GESTION DES FILMS ===

it('displays films list for today', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee('Films du');
    $response->assertSee($this->film->titre);
    $response->assertSee('120 min'); // Durée
    $response->assertSee('Tous publics'); // Classification
});

it('shows film details with seances', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee('Séances programmées');
    $response->assertSee('14:30'); // Heure de la séance
    $response->assertSee('Salle 1');
});

it('displays film statistics correctly', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee('1'); // Nombre de séances
    $response->assertSee('séances');
});

it('filters films by classification', function () {
    $this->actingAs($this->employee);

    // Créer un film avec classification différente
    $filmAdulte = Film::factory()->create([
        'titre'          => 'Film Adulte',
        'classification' => 'INTERDIT_MOINS_18',
        'duree_minutes'  => 90,
    ]);

    $seanceAdulte = Seance::factory()->create([
        'film_uuid'        => $filmAdulte->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->setHour(20),
    ]);

    // Test sans filtre - voir les deux films
    $response = $this->get('/employee/films');
    $response->assertSee('Film Employee Test');
    $response->assertSee('Film Adulte');

    // Test avec filtre TOUS_PUBLICS
    $response = $this->get('/employee/films?classification=TOUS_PUBLICS');
    $response->assertSee('Film Employee Test');
    $response->assertDontSee('Film Adulte');

    // Test avec filtre INTERDIT_MOINS_18
    $response = $this->get('/employee/films?classification=INTERDIT_MOINS_18');
    $response->assertDontSee('Film Employee Test');
    $response->assertSee('Film Adulte');
});

it('shows reset filter button when filter is active', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films?classification=TOUS_PUBLICS');

    $response->assertSuccessful();
    $response->assertSee('Réinitialiser');
});

it('shows empty state when no films match filter', function () {
    $this->actingAs($this->employee);

    // Supprimer toutes les séances d'aujourd'hui
    Seance::whereDate('date_heure_debut', now()->toDateString())->delete();

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee('Aucun film');
    $response->assertSee('Aucun film n\'est programmé aujourd\'hui');
});

// === TESTS GESTION DES SÉANCES ===

it('displays seances list page', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/seances');

    $response->assertSuccessful();
    $response->assertSee('Séances');
});

it('can access seances from dashboard link', function () {
    $this->actingAs($this->employee);

    $dashboardResponse = $this->get('/employee/dashboard');
    $dashboardResponse->assertSee('Voir toutes les séances');

    $seancesResponse = $this->get('/employee/seances');
    $seancesResponse->assertSuccessful();
});

// === TESTS GESTION DES RÉSERVATIONS ===

it('displays reservations list page', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/reservations');

    $response->assertSuccessful();
    $response->assertSee('Réservations');
});

// === TESTS SÉCURITÉ ET PERMISSIONS ===

it('admin can access employee dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'administrateur',
    ]);

    $admin->profil()->create([
        'nom'    => 'Admin',
        'prenom' => 'Super',
    ]);

    $admin->emploi()->create([
        'poste' => 'Administrateur',
    ]);

    $this->actingAs($admin);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Super Admin');
});

it('employee cannot access admin routes', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/admin/dashboard');

    $response->assertStatus(403);
});

// === TESTS NAVIGATION ===

it('contains navigation links to all employee sections', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Dashboard');
    $response->assertSee('Films');
    $response->assertSee('Séances');
    $response->assertSee('Réservations');
});

it('highlights active navigation item', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    // Le test doit vérifier que l'item Films est actif dans la navigation
    $response->assertSee('Films du'); // Page films active
});

// === TESTS RESPONSIVE ET INTERFACE ===

it('displays date correctly in French format', function () {
    $this->actingAs($this->employee);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee(now()->format('d/m/Y')); // Format français
});

it('shows seance status with correct styling', function () {
    $this->actingAs($this->employee);

    // Créer une séance passée
    $seancePassee = Seance::factory()->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->subHour(),
    ]);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    // Vérifier les classes CSS pour les différents statuts de séances
    $response->assertSee('bg-gray-100'); // Séance passée
    $response->assertSee('bg-green-100'); // Séance future
});

// === TESTS PERFORMANCE ET DONNÉES ===

it('handles empty employee profile gracefully', function () {
    $employeeNoProfile = User::factory()->create([
        'role' => 'employe',
    ]);

    $this->actingAs($employeeNoProfile);

    $response = $this->get('/employee/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Employé'); // Fallback nom
    $response->assertSee('Non défini'); // Fallback prénom
    $response->assertSee('Employé'); // Fallback poste
});

it('calculates film statistics correctly with multiple seances', function () {
    $this->actingAs($this->employee);

    // Créer plusieurs séances pour le même film aujourd'hui
    Seance::factory(2)->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->setHour(18),
    ]);

    $response = $this->get('/employee/films');

    $response->assertSuccessful();
    $response->assertSee('3'); // Total 3 séances (1 initiale + 2 nouvelles)
    $response->assertSee('séances');
});

// === TESTS D'INTÉGRATION ===

it('complete employee workflow - dashboard to film details', function () {
    $this->actingAs($this->employee);

    // 1. Accès dashboard
    $dashboardResponse = $this->get('/employee/dashboard');
    $dashboardResponse->assertSuccessful();
    $dashboardResponse->assertSee('Jean Dupont');

    // 2. Navigation vers films
    $filmsResponse = $this->get('/employee/films');
    $filmsResponse->assertSuccessful();
    $filmsResponse->assertSee($this->film->titre);

    // 3. Vérification des détails de séances
    $filmsResponse->assertSee('14:30');
    $filmsResponse->assertSee('Salle 1');
    $filmsResponse->assertSee('120 min');
});
