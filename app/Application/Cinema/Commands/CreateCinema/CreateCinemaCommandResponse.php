<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\CreateCinema;

final readonly class CreateCinemaCommandResponse
{
    public function __construct(
        public string $cinemaUuid,
        public string $nom,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    /**
     * @return array{cinema_uuid: string, nom: string, success: bool, message: string|null}
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cinema_uuid' => $this->cinemaUuid,
            'nom'         => $this->nom,
            'success'     => $this->success,
            'message'     => $this->message,
        ];
    }
}
