<?php

declare(strict_types=1);

namespace App\Application\Users\DTOs;

final readonly class ClientFormDto
{
    /**
     * @param array<string> $permissions
     * @param array<string> $roles
     */
    public function __construct(
        public ?string $uuid = null,
        public string $nom = '',
        public string $prenom = '',
        public string $email = '',
        public string $telephone = '',
        public bool $estActif = true,
        public ?string $dateNaissance = null,
        public ?string $sexe = null,
        public ?string $adresse = null,
        public ?string $ville = null,
        public ?string $codePostal = null,
        public string $pays = 'FR',
        public array $permissions = [],
        public array $roles = [],
        public bool $newsletter = false,
    ) {}

    /**
     * Créer un DTO depuis un UserDetailDto pour l'édition
     */
    public static function fromDetailDto(UserDetailDto $detail): self
    {
        return new self(
            uuid: $detail->uuid,
            nom: $detail->nom,
            prenom: $detail->prenom,
            email: $detail->email,
            telephone: $detail->telephone,
            estActif: $detail->estActif,
            dateNaissance: $detail->dateNaissance,
            sexe: $detail->sexe,
            adresse: $detail->adresse,
            ville: $detail->ville,
            codePostal: $detail->codePostal,
            pays: $detail->pays ?? 'FR',
            permissions: $detail->permissions,
            roles: $detail->roles,
            newsletter: false, // À récupérer depuis le profil si nécessaire
        );
    }

    /**
     * Créer un DTO vide pour la création
     */
    public static function empty(): self
    {
        return new self;
    }

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
            'est_actif'      => $this->estActif,
            'date_naissance' => $this->dateNaissance,
            'sexe'           => $this->sexe,
            'adresse'        => $this->adresse,
            'ville'          => $this->ville,
            'code_postal'    => $this->codePostal,
            'pays'           => $this->pays,
            'permissions'    => $this->permissions,
            'roles'          => $this->roles,
            'newsletter'     => $this->newsletter,
        ];
    }
}
