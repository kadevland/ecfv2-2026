<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Cinema\Entities\Film;
use App\Domain\Shared\Events\DomainEvent;

/**
 * Événement déclenché lors de la mise à jour d'un film
 */
final class FilmUpdated extends DomainEvent
{
    private function __construct(
        private readonly string $filmUuid
    ) {
        parent::__construct();
    }

    public static function fromFilm(Film $film): self
    {
        return new self($film->id->value);
    }

    public static function fromUuid(string $filmUuid): self
    {
        return new self($filmUuid);
    }

    public function getEventName(): string
    {
        return 'cinema.film.updated';
    }

    public function getAggregateId(): string
    {
        return $this->filmUuid;
    }

    public function getAggregateType(): string
    {
        return 'film';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'film_uuid' => $this->filmUuid,
        ];
    }

    public function getFilmUuid(): string
    {
        return $this->filmUuid;
    }
}
