<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\UpdateCinema;

final readonly class UpdateCinemaCommandResponse
{
    /**
     * @param string[] $updatedFields
     */
    public function __construct(
        public string $cinemaUuid,
        public string $nom,
        public bool $success = true,
        public ?string $message = null,
        public array $updatedFields = [],
    ) {}

    /**
     * @return array{cinema_uuid: string, nom: string, success: bool, message: string|null, updated_fields: string[]}
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cinema_uuid'    => $this->cinemaUuid,
            'nom'            => $this->nom,
            'success'        => $this->success,
            'message'        => $this->message,
            'updated_fields' => $this->updatedFields,
        ];
    }
}
