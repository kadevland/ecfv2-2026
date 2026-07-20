<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemaDetail;

use App\Application\Cinema\DTOs\CinemaDetailDto;

final readonly class GetCinemaDetailQueryResponse
{
    public function __construct(
        public ?CinemaDetailDto $cinema,
        public bool $found = true,
        public ?string $message = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data'    => $this->cinema?->toArray(),
            'found'   => $this->found,
            'message' => $this->message,
        ];
    }
}
