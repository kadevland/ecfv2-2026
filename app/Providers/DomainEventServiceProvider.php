<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Service Provider pour les events du domaine
 * Configure la synchronisation PostgreSQL → MongoDB via events
 */
class DomainEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Cinema Events
        \App\Domain\Cinema\Events\CinemaCreated::class => [
            \App\Application\Cinema\Listeners\SyncCinemaToMongoDb::class . '@handleCinemaCreated',
        ],
        \App\Domain\Cinema\Events\CinemaUpdated::class => [
            \App\Application\Cinema\Listeners\SyncCinemaToMongoDb::class . '@handleCinemaUpdated',
        ],
        \App\Domain\Cinema\Events\CinemaStatusChanged::class => [
            \App\Application\Cinema\Listeners\SyncCinemaToMongoDb::class . '@handleCinemaStatusChanged',
        ],

        // Film Events
        \App\Domain\Cinema\Events\FilmCreated::class => [
            \App\Application\Cinema\Listeners\SyncFilmToMongoDb::class . '@handleFilmCreated',
        ],
        \App\Domain\Cinema\Events\FilmUpdated::class => [
            \App\Application\Cinema\Listeners\SyncFilmToMongoDb::class . '@handleFilmUpdated',
        ],
        \App\Domain\Cinema\Events\FilmDeleted::class => [
            \App\Application\Cinema\Listeners\SyncFilmToMongoDb::class . '@handleFilmDeleted',
        ],

        // Salle Events
        \App\Domain\Cinema\Events\SalleCreated::class => [
            \App\Application\Cinema\Listeners\SyncSalleToMongoDb::class . '@handleSalleCreated',
        ],
        \App\Domain\Cinema\Events\SalleUpdated::class => [
            \App\Application\Cinema\Listeners\SyncSalleToMongoDb::class . '@handleSalleUpdated',
        ],
        \App\Domain\Cinema\Events\SalleDeleted::class => [
            \App\Application\Cinema\Listeners\SyncSalleToMongoDb::class . '@handleSalleDeleted',
        ],

        // Seance Events
        \App\Domain\Cinema\Events\SeanceCreated::class => [
            \App\Application\Cinema\Listeners\SyncSeanceToMongoDb::class . '@handleSeanceCreated',
        ],
        \App\Domain\Cinema\Events\SeanceUpdated::class => [
            \App\Application\Cinema\Listeners\SyncSeanceToMongoDb::class . '@handleSeanceUpdated',
        ],
        \App\Domain\Cinema\Events\SeanceDeleted::class => [
            \App\Application\Cinema\Listeners\SyncSeanceToMongoDb::class . '@handleSeanceDeleted',
        ],
        \App\Domain\Cinema\Events\SeanceStatusChanged::class => [
            \App\Application\Cinema\Listeners\SyncSeanceToMongoDb::class . '@handleSeanceStatusChanged',
        ],

        // Reservation Events
        \App\Domain\Reservations\Events\ReservationCreated::class => [
            \App\Application\Reservations\Listeners\SyncReservationToMongoDb::class . '@handleReservationCreated',
        ],
        \App\Domain\Reservations\Events\ReservationConfirmed::class => [
            \App\Application\Reservations\Listeners\SyncReservationToMongoDb::class . '@handleReservationConfirmed',
        ],
        \App\Domain\Reservations\Events\ReservationCancelled::class => [
            \App\Application\Reservations\Listeners\SyncReservationToMongoDb::class . '@handleReservationCancelled',
        ],

        // Review Events
        \App\Domain\Reviews\Events\ReviewCreated::class => [
            \App\Application\Reviews\Listeners\SyncReviewsToMongoDb::class . '@handleReviewCreated',
        ],
        \App\Domain\Reviews\Events\ReviewModerated::class => [
            \App\Application\Reviews\Listeners\SyncReviewsToMongoDb::class . '@handleReviewModerated',
        ],
        \App\Domain\Reviews\Events\ReviewLiked::class => [
            \App\Application\Reviews\Listeners\SyncReviewsToMongoDb::class . '@handleReviewLiked',
        ],
        \App\Domain\Reviews\Events\ReviewSignaled::class => [
            \App\Application\Reviews\Listeners\SyncReviewsToMongoDb::class . '@handleReviewSignaled',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
