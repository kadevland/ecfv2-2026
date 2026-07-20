<?php

declare(strict_types=1);

namespace App\Application\Public\Seance\Queries\GetSeancesByFilm;

use Respect\Validation\Validator as v;
use App\Application\Contracts\QueryInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class GetSeancesByFilmQuery implements QueryInterface
{
    public function __construct(
        public string $filmId,
        public ?bool $futuresOnly = true,
        public ?int $limit = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation film ID
        try {
            v::stringType()->notEmpty()->assert($this->filmId);
        } catch (ValidationException $e) {
            $errors['filmId'] = 'L\'ID du film est requis';
        }

        // Validation limit
        if ($this->limit !== null) {
            try {
                v::intType()->positive()->max(100)->assert($this->limit);
            } catch (ValidationException $e) {
                $errors['limit'] = 'La limite doit être un entier positif maximum 100';
            }
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
