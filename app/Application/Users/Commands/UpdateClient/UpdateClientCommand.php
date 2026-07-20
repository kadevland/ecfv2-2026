<?php

declare(strict_types=1);

namespace App\Application\Users\Commands\UpdateClient;

use App\Domain\Shared\Enums\SexeEnum;
use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class UpdateClientCommand implements CommandInterface
{
    /**
     * @param array<string, string>|null $adresse
     */
    public function __construct(
        public string $userUuid,
        public ?string $prenom = null,
        public ?string $nom = null,
        public ?string $email = null,
        public ?string $telephone = null,
        public ?string $dateNaissance = null,
        public ?string $sexe = null,
        public ?array $adresse = null,
        public ?bool $isActive = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation UUID
        try {
            v::uuidV7()->assert($this->userUuid);
        } catch (ValidationException $e) {
            $errors['userUuid'] = 'L\'UUID de l\'utilisateur n\'est pas valide';
        }

        // Validation prénom (si fourni)
        if ($this->prenom !== null) {
            try {
                v::stringType()->notEmpty()->length(2, 50)->assert($this->prenom);
            } catch (ValidationException $e) {
                $errors['prenom'] = 'Le prénom doit contenir entre 2 et 50 caractères';
            }
        }

        // Validation nom (si fourni)
        if ($this->nom !== null) {
            try {
                v::stringType()->notEmpty()->length(2, 50)->assert($this->nom);
            } catch (ValidationException $e) {
                $errors['nom'] = 'Le nom doit contenir entre 2 et 50 caractères';
            }
        }

        // Validation email (si fourni)
        if ($this->email !== null) {
            try {
                v::email()->assert($this->email);
            } catch (ValidationException $e) {
                $errors['email'] = 'L\'email n\'est pas valide';
            }
        }

        // Validation sexe (si fourni)
        if ($this->sexe !== null) {
            try {
                v::in(SexeEnum::values())->assert($this->sexe);
            } catch (ValidationException $e) {
                $errors['sexe'] = 'Le sexe doit être : ' . implode(', ', SexeEnum::values());
            }
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
