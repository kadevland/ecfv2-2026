<?php

declare(strict_types=1);

namespace App\Application\Users\DTOs;

final readonly class UserDetailDto
{
    /**
     * @param array<string> $permissions
     * @param array<string> $roles
     */
    public function __construct(
        public string $uuid,
        public string $nom,
        public string $prenom,
        public string $email,
        public string $telephone,
        public string $type,
        public string $typeLabel,
        public bool $estActif,
        public bool $emailVerified,
        public ?string $dateNaissance = null,
        public ?string $sexe = null,
        public ?string $adresse = null,
        public ?string $ville = null,
        public ?string $codePostal = null,
        public ?string $pays = null,
        public ?string $poste = null,
        public ?string $departement = null,
        public ?float $salaire = null,
        public ?string $dateEmbauche = null,
        public ?string $avatar = null,
        public array $permissions = [],
        public array $roles = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'           => $this->uuid,
            'nom'            => $this->nom,
            'prenom'         => $this->prenom,
            'email'          => $this->email,
            'telephone'      => $this->telephone,
            'type'           => $this->type,
            'type_label'     => $this->typeLabel,
            'est_actif'      => $this->estActif,
            'email_verified' => $this->emailVerified,
            'date_naissance' => $this->dateNaissance,
            'sexe'           => $this->sexe,
            'adresse'        => $this->adresse,
            'ville'          => $this->ville,
            'code_postal'    => $this->codePostal,
            'pays'           => $this->pays,
            'poste'          => $this->poste,
            'departement'    => $this->departement,
            'salaire'        => $this->salaire,
            'date_embauche'  => $this->dateEmbauche,
            'avatar'         => $this->avatar,
            'permissions'    => $this->permissions,
            'roles'          => $this->roles,
            'created_at'     => $this->createdAt,
            'updated_at'     => $this->updatedAt,
        ];
    }

    public function isEmployee(): bool
    {
        return $this->type === 'employee';
    }

    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }
}
