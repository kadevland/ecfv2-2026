<?php

declare(strict_types=1);

use App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQuery;

describe('GetCinemasListQuery', function () {
    describe('Construction', function () {
        it('peut être créé avec les paramètres minimaux', function () {
            $query = new GetCinemasListQuery;

            expect($query->page)->toBe(1);
            expect($query->perPage)->toBe(20);
            expect($query->location)->toBeNull();
            expect($query->filters)->toBeNull();
        });

        it('peut être créé avec tous les paramètres', function () {
            $filters = ['ville' => 'Paris', 'pays' => 'FR'];
            $query   = new GetCinemasListQuery(
                page: 3,
                perPage: 15,
                location: 'Paris',
                filters: $filters
            );

            expect($query->page)->toBe(3);
            expect($query->perPage)->toBe(15);
            expect($query->location)->toBe('Paris');
            expect($query->filters)->toBe($filters);
        });

        it('utilise des valeurs par défaut cohérentes', function () {
            $query = new GetCinemasListQuery(page: 5);

            expect($query->page)->toBe(5);
            expect($query->perPage)->toBe(20); // Valeur par défaut
            expect($query->location)->toBeNull();
            expect($query->filters)->toBeNull();
        });
    });

    describe('Pagination', function () {
        it('accepte différentes tailles de page', function () {
            $query1 = new GetCinemasListQuery(perPage: 10);
            $query2 = new GetCinemasListQuery(perPage: 50);
            $query3 = new GetCinemasListQuery(perPage: 100);

            expect($query1->perPage)->toBe(10);
            expect($query2->perPage)->toBe(50);
            expect($query3->perPage)->toBe(100);
        });

        it('accepte différents numéros de page', function () {
            $query1 = new GetCinemasListQuery(page: 1);
            $query2 = new GetCinemasListQuery(page: 10);
            $query3 = new GetCinemasListQuery(page: 999);

            expect($query1->page)->toBe(1);
            expect($query2->page)->toBe(10);
            expect($query3->page)->toBe(999);
        });
    });

    describe('Filtres', function () {
        it('accepte des filtres simples', function () {
            $filters = ['ville' => 'Lyon'];
            $query   = new GetCinemasListQuery(filters: $filters);

            expect($query->filters)->toBe($filters);
        });

        it('accepte des filtres complexes', function () {
            $filters = [
                'ville'       => 'Paris',
                'pays'        => 'FR',
                'code_postal' => '75001',
                'region'      => ['Île-de-France', 'Normandie'],
            ];
            $query = new GetCinemasListQuery(filters: $filters);

            expect($query->filters)->toBe($filters);
        });

        it('accepte une localisation spécifique', function () {
            $query = new GetCinemasListQuery(location: 'Marseille, France');

            expect($query->location)->toBe('Marseille, France');
        });

        it('peut combiner localisation et filtres', function () {
            $filters = ['pays' => 'BE'];
            $query   = new GetCinemasListQuery(
                location: 'Bruxelles',
                filters: $filters
            );

            expect($query->location)->toBe('Bruxelles');
            expect($query->filters)->toBe($filters);
        });
    });

    describe('Scénarios d\'usage', function () {
        it('peut représenter une recherche par défaut', function () {
            $query = new GetCinemasListQuery;

            // Première page, 20 éléments, aucun filtre
            expect($query->page)->toBe(1);
            expect($query->perPage)->toBe(20);
            expect($query->location)->toBeNull();
            expect($query->filters)->toBeNull();
        });

        it('peut représenter une recherche paginée', function () {
            $query = new GetCinemasListQuery(page: 3, perPage: 12);

            expect($query->page)->toBe(3);
            expect($query->perPage)->toBe(12);
        });

        it('peut représenter une recherche géographique', function () {
            $query = new GetCinemasListQuery(
                location: 'Lyon 69000',
                filters: ['pays' => 'FR']
            );

            expect($query->location)->toBe('Lyon 69000');
            expect($query->filters['pays'])->toBe('FR');
        });

        it('peut représenter une recherche administrative', function () {
            $filters = [
                'ville'       => 'Paris',
                'code_postal' => '75001',
                'pays'        => 'FR',
            ];
            $query = new GetCinemasListQuery(
                page: 1,
                perPage: 50,
                filters: $filters
            );

            expect($query->page)->toBe(1);
            expect($query->perPage)->toBe(50);
            expect($query->filters)->toBe($filters);
        });
    });
});
