<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer les réservations d'un utilisateur
 */
final readonly class GetUserReservationsQuery implements QueryInterface
{
    public function __construct(
        public string $userId,
        public ?string $statut = null,
        public int $page = 1,
        public int $perPage = 10,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->userId)
            && $this->page > 0
            && $this->perPage > 0;
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];
        if (empty($this->userId)) {
            $errors['userId'] = 'L\'identifiant utilisateur est requis';
        }
        if ($this->page <= 0) {
            $errors['page'] = 'Le numéro de page doit être positif';
        }
        if ($this->perPage <= 0) {
            $errors['perPage'] = 'Le nombre d\'éléments par page doit être positif';
        }

        return $errors;
    }
}
