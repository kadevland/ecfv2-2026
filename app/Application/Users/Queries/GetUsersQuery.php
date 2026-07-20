<?php

declare(strict_types=1);

namespace App\Application\Users\Queries;

use App\Application\Contracts\QueryInterface;

final readonly class GetUsersQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $type = null, // 'client', 'employee', 'admin'
        public readonly ?string $search = null,
        public readonly ?bool $estActif = true,
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
    ) {}

    public static function all(): self
    {
        return new self;
    }

    public static function clients(): self
    {
        return new self(type: 'client');
    }

    public static function employees(): self
    {
        return new self(type: 'employee');
    }

    public static function admins(): self
    {
        return new self(type: 'admin');
    }

    public static function search(string $search): self
    {
        return new self(search: $search);
    }

    public function withPagination(int $page, int $perPage): self
    {
        return new self(
            type: $this->type,
            search: $this->search,
            estActif: $this->estActif,
            page: $page,
            perPage: $perPage,
            sortBy: $this->sortBy,
            sortDirection: $this->sortDirection,
        );
    }

    public function sortBy(string $field, string $direction = 'asc'): self
    {
        return new self(
            type: $this->type,
            search: $this->search,
            estActif: $this->estActif,
            page: $this->page,
            perPage: $this->perPage,
            sortBy: $field,
            sortDirection: $direction,
        );
    }
}
