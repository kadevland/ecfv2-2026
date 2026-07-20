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
    // Créer un utilisateur de test
    $this->user = User::factory()->create([
        'email'    => 'user@test.com',
        'password' => bcrypt('password123'),
    ]);

    // Créer un cinéma avec salle
    $this->cinema = Cinema::factory()->create([
        'nom'   => 'Cinéma Test',
        'actif' => true,
    ]);

    $this->salle = Salle::factory()->create([
        'cinema_uuid' => $this->cinema->uuid,
        'nom'         => 'Salle 1',
        'capacite'    => 100,
    ]);

    // Créer un film
    $this->film = Film::factory()->create([
        'titre'          => 'Film Test',
        'duree_minutes'  => 120,
        'classification' => 'TOUS_PUBLICS',
        'genres'         => ['action', 'aventure'],
    ]);

    // Créer une séance
    $this->seance = Seance::factory()->create([
        'film_uuid'        => $this->film->uuid,
        'salle_uuid'       => $this->salle->uuid,
        'date_heure_debut' => now()->addDay(),
        'date_heure_fin'   => now()->addDay()->addHours(2),
        'tarif_normal'     => 12.50,
        'tarif_reduit'     => 9.00,
    ]);
});

it('displays film catalogue page', function () {
    $response = $this->get('/films');

    $response->assertSuccessful();
    $response->assertSee('Films à l\'affiche');
});

it('displays film details page', function () {
    $response = $this->get("/films/{$this->film->uuid}");

    $response->assertSuccessful();
    $response->assertSee($this->film->titre);
    $response->assertSee('120 min');
});

it('displays available seances for a film', function () {
    $response = $this->get("/films/{$this->film->uuid}/seances");

    $response->assertSuccessful();
    $response->assertSee('Séances disponibles');
    $response->assertSee('Salle 1');
});

it('can access seat selection page for a seance', function () {
    $response = $this->get("/seance/{$this->seance->uuid}/reserver");

    $response->assertSuccessful();
    $response->assertSee('Sélection des places');
    $response->assertSee($this->film->titre);
});

it('redirects unauthenticated user to login when creating reservation', function () {
    $response = $this->post('/reservation', [
        'seance_uuid' => $this->seance->uuid,
        'places'      => ['A1', 'A2'],
        'tarifs'      => ['normal', 'reduit'],
    ]);

    $response->assertRedirect('/login');
});

it('can create reservation when authenticated', function () {
    $this->actingAs($this->user);

    $response = $this->post('/reservation', [
        'seance_uuid'      => $this->seance->uuid,
        'places'           => ['A1', 'A2'],
        'tarifs'           => ['normal', 'reduit'],
        'nom_client'       => 'Test User',
        'email_client'     => 'user@test.com',
        'telephone_client' => '0123456789',
    ]);

    $response->assertRedirect('/confirmation');

    // Vérifier que la réservation a été créée
    $this->assertDatabaseHas('reservations', [
        'user_uuid'   => $this->user->uuid,
        'seance_uuid' => $this->seance->uuid,
        'statut'      => 'EN_ATTENTE',
    ]);
});

it('validates reservation form correctly', function () {
    $this->actingAs($this->user);

    $response = $this->post('/reservation', [
        'seance_uuid' => '',
        'places'      => [],
        'tarifs'      => [],
    ]);

    $response->assertSessionHasErrors(['seance_uuid', 'places', 'tarifs']);
});

it('displays reservation confirmation page', function () {
    $this->actingAs($this->user);

    // Créer une réservation
    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'EN_ATTENTE',
        'numero_reservation' => 'RES-2025-001',
        'total'              => 21.50,
    ]);

    $response = $this->get('/confirmation?reservation=' . $reservation->numero_reservation);

    $response->assertSuccessful();
    $response->assertSee('Réservation confirmée');
    $response->assertSee($reservation->numero_reservation);
    $response->assertSee('21,50 €');
});

it('can process payment for reservation', function () {
    $this->actingAs($this->user);

    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'EN_ATTENTE',
        'numero_reservation' => 'RES-2025-002',
        'total'              => 21.50,
    ]);

    $response = $this->post('/reservation/payment', [
        'reservation_id' => $reservation->uuid,
        'payment_method' => 'carte',
        'card_number'    => '4111111111111111',
        'card_expiry'    => '12/25',
        'card_cvc'       => '123',
    ]);

    $response->assertRedirect('/confirmation');

    // Vérifier que le statut a changé
    $this->assertDatabaseHas('reservations', [
        'uuid'   => $reservation->uuid,
        'statut' => 'CONFIRMEE',
    ]);
});

it('prevents booking the same seats twice', function () {
    $this->actingAs($this->user);

    // Créer première réservation
    Reservation::factory()->create([
        'user_uuid'        => $this->user->uuid,
        'seance_uuid'      => $this->seance->uuid,
        'statut'           => 'CONFIRMEE',
        'places_reservees' => ['A1', 'A2'],
    ]);

    // Tenter de réserver les mêmes places
    $response = $this->post('/reservation', [
        'seance_uuid'      => $this->seance->uuid,
        'places'           => ['A1', 'A3'],
        'tarifs'           => ['normal', 'normal'],
        'nom_client'       => 'Test User 2',
        'email_client'     => 'user2@test.com',
        'telephone_client' => '0123456788',
    ]);

    $response->assertSessionHasErrors(['places']);
    $response->assertSessionHas('error', 'Certaines places sont déjà réservées');
});

it('calculates correct total for reservation', function () {
    $this->actingAs($this->user);

    $response = $this->post('/reservation', [
        'seance_uuid'      => $this->seance->uuid,
        'places'           => ['A1', 'A2', 'A3'],
        'tarifs'           => ['normal', 'reduit', 'normal'],
        'nom_client'       => 'Test User',
        'email_client'     => 'user@test.com',
        'telephone_client' => '0123456789',
    ]);

    // 2 * 12.50 + 1 * 9.00 = 34.00
    $this->assertDatabaseHas('reservations', [
        'user_uuid' => $this->user->uuid,
        'total'     => 34.00,
    ]);
});

it('sends confirmation email after successful reservation', function () {
    $this->actingAs($this->user);

    Mail::fake();

    $response = $this->post('/reservation', [
        'seance_uuid'      => $this->seance->uuid,
        'places'           => ['A1'],
        'tarifs'           => ['normal'],
        'nom_client'       => 'Test User',
        'email_client'     => 'user@test.com',
        'telephone_client' => '0123456789',
    ]);

    Mail::assertSent(\App\Mail\ReservationConfirmation::class);
});

it('displays user reservations in account page', function () {
    $this->actingAs($this->user);

    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'RES-2025-003',
    ]);

    $response = $this->get('/mon-compte/reservations');

    $response->assertSuccessful();
    $response->assertSee('Mes réservations');
    $response->assertSee($reservation->numero_reservation);
});

it('can generate QR code for confirmed reservation', function () {
    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'RES-2025-004',
    ]);

    $response = $this->get('/qr/' . $reservation->numero_reservation);

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'image/png');
});

it('can download PDF ticket for confirmed reservation', function () {
    $reservation = Reservation::factory()->create([
        'user_uuid'          => $this->user->uuid,
        'seance_uuid'        => $this->seance->uuid,
        'statut'             => 'CONFIRMEE',
        'numero_reservation' => 'RES-2025-005',
    ]);

    $response = $this->get('/billet/' . $reservation->numero_reservation);

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');
});
