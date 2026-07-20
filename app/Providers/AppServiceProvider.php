<?php

declare(strict_types=1);

namespace App\Providers;

use App\Observers\UserObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Listeners\MongoDB\FilmEventListener;
use App\Listeners\MongoDB\ReviewEventListener;
use App\Infrastructure\Database\Models\Auth\User;
use App\Listeners\MongoDB\ReservationEventListener;
use App\Infrastructure\Events\LaravelEventDispatcher;
use App\Application\Shared\Events\EventDispatcherInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Infrastructure\Repositories\Cinema\EloquentFilmRepository;
use App\Infrastructure\Repositories\Cinema\EloquentSalleRepository;
use App\Infrastructure\Repositories\Cinema\EloquentCinemaRepository;
use App\Infrastructure\Repositories\Cinema\EloquentSeanceRepository;



final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            CinemaRepositoryInterface::class,
            EloquentCinemaRepository::class
        );

        $this->app->bind(
            FilmRepositoryInterface::class,
            EloquentFilmRepository::class
        );

        $this->app->bind(
            SalleRepositoryInterface::class,
            EloquentSalleRepository::class
        );

        $this->app->bind(
            SeanceRepositoryInterface::class,
            EloquentSeanceRepository::class
        );

        // Cinema domain reservation binding
        $this->app->bind(
            \App\Domain\Cinema\Repositories\ReservationRepositoryInterface::class,
            \App\Infrastructure\Repositories\Cinema\EloquentReservationRepository::class
        );

        // Reservations domain reservation binding
        $this->app->bind(
            \App\Domain\Reservations\Repositories\ReservationRepositoryInterface::class,
            \App\Infrastructure\Repositories\Reservations\EloquentReservationRepository::class
        );

        // Event system bindings
        $this->app->bind(
            EventDispatcherInterface::class,
            LaravelEventDispatcher::class
        );

        // Application handlers bindings
        $this->app->bind(
            \App\Application\Cinema\Handlers\CreateSalleHandler::class
        );

        $this->app->bind(
            \App\Application\Cinema\Queries\GetSalles\GetSallesQueryHandler::class
        );

        $this->app->bind(
            \App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQueryHandler::class
        );

        $this->app->bind(
            \App\Domain\Users\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\Repositories\Users\EloquentUserRepository::class
        );

        // UserProfil Repository binding (CQRS direct access)
        $this->app->bind(
            \App\Domain\User\Repositories\UserProfilRepositoryInterface::class,
            \App\Infrastructure\Repositories\User\EloquentUserProfilRepository::class
        );

        // Employee Repository binding
        /*$this->app->bind(
            \App\Domain\Users\Repositories\EmployeRepositoryInterface::class,
            \App\Infrastructure\Repositories\Users\EloquentEmployeRepository::class
        );*/

        // Incident Repository binding
        $this->app->bind(
            \App\Domain\Employees\Repositories\IncidentRepositoryInterface::class,
            \App\Infrastructure\Persistence\PostgreSQL\IncidentRepository::class
        );

        // Emploi Repository binding
        $this->app->bind(
            \App\Domain\Employees\Repositories\EmploiRepositoryInterface::class,
            \App\Infrastructure\Database\Repositories\Employees\EloquentEmploiRepository::class
        );

        // Public Seance Repository binding (MongoDB)
        $this->app->bind(
            \App\Domain\Public\Repositories\SeancePublicRepositoryInterface::class,
            \App\Infrastructure\Repositories\Public\MongoSeancePublicRepository::class
        );

        // Public Seance QueryHandler binding
        $this->app->bind(
            \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQueryHandler::class
        );

        // Client Profils QueryHandler binding (CQRS optimized)
        $this->app->bind(
            \App\Application\Users\Queries\GetClientProfils\GetClientProfilsQueryHandler::class
        );

        // Employee Profils QueryHandler binding (CQRS optimized)
        $this->app->bind(
            \App\Application\Users\Queries\GetEmployeProfils\GetEmployeProfilsQueryHandler::class
        );

        // Public repositories bindings (MongoDB)
        $this->app->bind(
            \App\Domain\Public\Repositories\FilmRepositoryInterface::class,
            \App\Infrastructure\Repositories\Public\FilmRepository::class
        );

        $this->app->bind(
            \App\Domain\Public\Repositories\SeanceRepositoryInterface::class,
            \App\Infrastructure\Repositories\Public\SeanceRepository::class
        );

        $this->app->bind(
            \App\Domain\Public\Repositories\SearchRepositoryInterface::class,
            \App\Infrastructure\Repositories\Public\SearchRepository::class
        );

        $this->app->bind(
            \App\Domain\Public\Repositories\HealthCheckRepositoryInterface::class,
            \App\Infrastructure\Repositories\Public\HealthCheckRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer les Event Listeners MongoDB
        // FilmEventListener remplacé par DomainEventServiceProvider
        // CinemaEventListener remplacé par DomainEventServiceProvider
        // SeanceEventListener remplacé par DomainEventServiceProvider
        Event::subscribe(ReservationEventListener::class);
        Event::subscribe(ReviewEventListener::class);

        // Enregistrer UserObserver pour synchronisation CQRS
        User::observe(UserObserver::class);
    }
}
