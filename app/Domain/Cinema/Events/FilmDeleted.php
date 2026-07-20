<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\FilmId;

final class FilmDeleted extends DomainEvent
{
    private function __construct(
        private readonly string $filmUuid
    ) {
        parent::__construct();
    }

    public static function create(FilmId $filmId, string $titre): self
    {
        return new self($filmId->value);
    }

    public static function fromUuid(string $filmUuid): self
    {
        return new self($filmUuid);
    }

    public function getEventName(): string
    {
        return 'cinema.film.deleted';
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
