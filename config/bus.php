<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Command Mappings
    |--------------------------------------------------------------------------
    |
    | Mappings explicites Command -> Handler
    |
    */

    'command_mappings' => [
        // Cinema Commands
        \App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommand::class => \App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommandHandler::class,

        \App\Application\Cinema\Commands\UpdateCinema\UpdateCinemaCommand::class => \App\Application\Cinema\Commands\UpdateCinema\UpdateCinemaCommandHandler::class,

        \App\Application\Cinema\Commands\ToggleCinemaStatus\ToggleCinemaStatusCommand::class => \App\Application\Cinema\Commands\ToggleCinemaStatus\ToggleCinemaStatusCommandHandler::class,

        // Film Commands
        \App\Application\Film\Commands\CreateFilm\CreateFilmCommand::class => \App\Application\Film\Commands\CreateFilm\CreateFilmCommandHandler::class,

        \App\Application\Film\Commands\UpdateFilm\UpdateFilmCommand::class => \App\Application\Film\Commands\UpdateFilm\UpdateFilmCommandHandler::class,

        // Salle Commands
        \App\Application\Salle\Commands\CreateSalle\CreateSalleCommand::class => \App\Application\Salle\Commands\CreateSalle\CreateSalleCommandHandler::class,

        \App\Application\Salle\Commands\UpdateSalle\UpdateSalleCommand::class => \App\Application\Salle\Commands\UpdateSalle\UpdateSalleCommandHandler::class,

        // Seance Commands
        \App\Application\Seance\Commands\CreateSeance\CreateSeanceCommand::class => \App\Application\Seance\Commands\CreateSeance\CreateSeanceCommandHandler::class,

        \App\Application\Seance\Commands\UpdateSeance\UpdateSeanceCommand::class => \App\Application\Seance\Commands\UpdateSeance\UpdateSeanceCommandHandler::class,

        // Incident Commands
        \App\Application\Employees\Commands\DeclareIncident\DeclareIncidentCommand::class => \App\Application\Employees\Commands\DeclareIncident\DeclareIncidentCommandHandler::class,

        // User Commands
        \App\Application\Users\Commands\UpdateEmployee\UpdateEmployeeCommand::class => \App\Application\Users\Commands\UpdateEmployee\UpdateEmployeeCommandHandler::class,
        \App\Application\Users\Commands\UpdateClient\UpdateClientCommand::class     => \App\Application\Users\Commands\UpdateClient\UpdateClientCommandHandler::class,

        // Employee Job Commands
        \App\Application\Employees\Commands\UpdateEmployeeJob\UpdateEmployeeJobCommand::class => \App\Application\Employees\Commands\UpdateEmployeeJob\UpdateEmployeeJobCommandHandler::class,

        // Reservation Commands
        \App\Application\Reservations\Commands\CreateReservationCommand::class    => \App\Application\Reservations\Handlers\CreateReservationHandler::class,
        \App\Application\Reservations\Commands\ProcessPaymentCommand::class       => \App\Application\Reservations\Handlers\ProcessPaymentHandler::class,
        \App\Application\Reservations\Commands\SendReservationEmailCommand::class => \App\Application\Reservations\Handlers\SendReservationEmailHandler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Mappings
    |--------------------------------------------------------------------------
    |
    | Mappings explicites Query -> Handler
    |
    */

    'query_mappings' => [
        // Cinema Queries (Admin - PostgreSQL)
        \App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQuery::class => \App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQueryHandler::class,

        \App\Application\Cinema\Queries\GetCinemaDetail\GetCinemaDetailQuery::class => \App\Application\Cinema\Queries\GetCinemaDetail\GetCinemaDetailQueryHandler::class,

        \App\Application\Cinema\Queries\GetCinemasForSelectQuery::class => \App\Application\Cinema\Handlers\GetCinemasForSelectHandler::class,

        // Cinema Queries (Public - MongoDB)
        \App\Application\Public\Cinema\Queries\GetPublicCinemaDetail\GetPublicCinemaDetailQuery::class => \App\Application\Public\Cinema\Queries\GetPublicCinemaDetail\GetPublicCinemaDetailQueryHandler::class,

        \App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQuery::class => \App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQueryHandler::class,

        // Seance Queries (Public - MongoDB)
        \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQuery::class => \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQueryHandler::class,

        // Film Queries
        \App\Application\Film\Queries\GetFilmsList\GetFilmsListQuery::class => \App\Application\Film\Queries\GetFilmsList\GetFilmsListQueryHandler::class,

        \App\Application\Film\Queries\GetFilmDetail\GetFilmDetailQuery::class => \App\Application\Film\Queries\GetFilmDetail\GetFilmDetailQueryHandler::class,

        // Salle Queries
        \App\Application\Salle\Queries\GetSallesList\GetSallesListQuery::class => \App\Application\Salle\Queries\GetSallesList\GetSallesListQueryHandler::class,

        \App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQuery::class => \App\Application\Salle\Queries\GetSalleDetail\GetSalleDetailQueryHandler::class,

        \App\Application\Salle\Queries\GetSalleForEdit\GetSalleForEditQuery::class => \App\Application\Salle\Queries\GetSalleForEdit\GetSalleForEditQueryHandler::class,

        // Seance Queries
        \App\Application\Seance\Queries\GetSeancesList\GetSeancesListQuery::class => \App\Application\Seance\Queries\GetSeancesList\GetSeancesListQueryHandler::class,

        \App\Application\Seance\Queries\GetSeanceDetail\GetSeanceDetailQuery::class => \App\Application\Seance\Queries\GetSeanceDetail\GetSeanceDetailQueryHandler::class,

        // Reservation Queries
        \App\Application\Reservations\Queries\GetReservationDetailQuery::class => \App\Application\Reservations\Handlers\GetReservationDetailHandler::class,
        \App\Application\Reservations\Queries\GetReservationsQuery::class      => \App\Application\Reservations\Handlers\GetReservationsHandler::class,
        \App\Application\Reservations\Queries\GetReservationQuery::class       => \App\Application\Reservations\Handlers\GetReservationHandler::class,
        // \App\Application\Reservations\Queries\GetUserReservationsQuery::class => \App\Application\Reservations\Queries\GetUserReservationsQueryHandler::class,
        \App\Application\Reservations\Queries\GetReservationByNumberQuery::class => \App\Application\Reservations\Handlers\GetReservationByNumberHandler::class,

        // Incident Queries
        \App\Application\Employees\Queries\GetIncidentsList\GetIncidentsListQuery::class => \App\Application\Employees\Queries\GetIncidentsList\GetIncidentsListQueryHandler::class,

        // User Queries
        \App\Application\Users\Queries\GetUserDetailQuery::class    => \App\Application\Users\Queries\GetUserDetail\GetUserDetailQueryHandler::class,
        \App\Application\Users\Queries\GetClientProfilsQuery::class => \App\Application\Users\Queries\GetClientProfils\GetClientProfilsQueryHandler::class,
    ],

];
