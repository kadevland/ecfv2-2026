<?php

declare(strict_types=1);

namespace App\Application\Users\Queries;

use App\Application\Contracts\QueryInterface;

final readonly class GetClientProfilsQuery implements QueryInterface
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 20,
        public ?string $search = null,
        public ?bool $estActif = null,
    ) {}

    public static function create(
        int $page = 1,
        int $perPage = 20,
        ?string $search = null,
        ?bool $estActif = null,
    ): self {
        return new self($page, $perPage, $search, $estActif);
    }

    public function isValid(): bool
    {
        return $this->page > 0
            && $this->perPage > 0
            && $this->perPage <= 100;
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->page <= 0) {
            $errors['page'] = 'La page doit être supérieure à 0';
        }

        if ($this->perPage <= 0) {
            $errors['perPage'] = 'Le nombre par page doit être supérieur à 0';
        }

        if ($this->perPage > 100) {
            $errors['perPage'] = 'Le nombre par page ne peut pas dépasser 100';
        }

        return $errors;
    }
}
