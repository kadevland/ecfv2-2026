<?php

declare(strict_types=1);

namespace App\Application\Users\Queries;

use App\Application\Contracts\QueryInterface;

final readonly class GetUserDetailQuery implements QueryInterface
{
    public function __construct(
        public string $userUuid,
        public bool $includeProfile = true,
        public bool $includeReservations = false,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->userUuid);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->userUuid)) {
            $errors[] = 'UUID de l\'utilisateur requis';
        }

        return $errors;
    }
}
