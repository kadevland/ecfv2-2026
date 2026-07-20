<?php

declare(strict_types=1);

it('can access employee dashboard main page', function () {
    $response = $this->get('/gestion');

    $response->assertSuccessful();
    $response->assertSee('Dashboard Employé');
    $response->assertSee('Cinéphoria Centre-Ville');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('can access employee sessions today page', function () {
    $response = $this->get('/gestion/seances/aujourd-hui');

    $response->assertSuccessful();
    $response->assertSee('Séances du jour');
    $response->assertSee('Total séances');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('can access employee sessions week page', function () {
    $response = $this->get('/gestion/seances/semaine');

    $response->assertSuccessful();
    $response->assertSee('Séances de la semaine');
    $response->assertSee('Vue d\'ensemble hebdomadaire');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('can access employee reservations today page', function () {
    $response = $this->get('/gestion/reservations/aujourd-hui');

    $response->assertSuccessful();
    $response->assertSee('Réservations du jour');
    $response->assertSee('Total réservations');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('employee dashboard contains navigation links', function () {
    $response = $this->get('/gestion');

    $response->assertSuccessful();
    $response->assertSee('Séances d\'aujourd\'hui');
    $response->assertSee('Séances de la semaine');
    $response->assertSee('Réservations du jour');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('employee dashboard displays statistics', function () {
    $response = $this->get('/gestion');

    $response->assertSuccessful();
    $response->assertSee('séance(s) programmée(s)');
    $response->assertSee('réservation(s) en cours');
    $response->assertSee('de revenus');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('employee sessions today page displays session details', function () {
    $response = $this->get('/gestion/seances/aujourd-hui');

    $response->assertSuccessful();
    $response->assertSee('Planning détaillé des séances');
    $response->assertSee('Chiffre d\'affaires');
    $response->assertSee('Occupation moyenne');
})->skip('Routes obsolètes - nouvelles routes sous /employee');

it('employee reservations today page displays reservation stats', function () {
    $response = $this->get('/gestion/reservations/aujourd-hui');

    $response->assertSuccessful();
    $response->assertSee('Réservations par statut');
    $response->assertSee('Confirmées');
    $response->assertSee('Payées');
})->skip('Routes obsolètes - nouvelles routes sous /employee');
