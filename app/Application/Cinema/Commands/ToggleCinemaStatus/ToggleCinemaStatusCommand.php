<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands\ToggleCinemaStatus;

use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class ToggleCinemaStatusCommand implements CommandInterface
{
    public function __construct(
        public string $cinemaUuid,
    ) {}

    /**
     * Valide les données de la command
     *
     * @return array<string, string> Tableau des erreurs (vide si valide)
     */
    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation UUID
        try {
            v::uuidV7()->assert($this->cinemaUuid);
        } catch (ValidationException $e) {
            $errors['cinemaUuid'] = 'L\'UUID du cinéma n\'est pas valide';
        }

        return $errors;
    }

    /**
     * Vérifie si la command est valide
     */
    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
