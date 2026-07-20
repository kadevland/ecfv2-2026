<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Respect\Validation\Validator as v;
use Illuminate\Support\Facades\Validator;

final readonly class Address
{
    // Mapping keys
    public const KEY_RUE = 'rue';

    public const KEY_VILLE = 'ville';

    public const KEY_CODE_POSTAL = 'code_postal';

    public const KEY_PAYS = 'pays';

    public const KEY_COMPLEMENT = 'complement';

    // Champs requis pour validation
    private const REQUIRED_FIELDS = [
        self::KEY_RUE,
        self::KEY_VILLE,
        self::KEY_CODE_POSTAL,
        self::KEY_PAYS,
    ];

    private const MAX_RUE_LENGTH = 255;

    private const MAX_VILLE_LENGTH = 100;

    private const MAX_PAYS_LENGTH = 100;

    private const MAX_COMPLEMENT_LENGTH = 255;

    private const CODE_POSTAL_PATTERN = '/^[0-9A-Z\-\s]{2,10}$/i';

    private const CODE_POSTAL_MIN_LENGTH = 2;

    private const CODE_POSTAL_MAX_LENGTH = 10;

    public function __construct(
        public readonly string $rue,
        public readonly string $ville,
        public readonly string $codePostal,
        public readonly string $pays,
        public readonly ?string $complement = null,
    ) {
        $this->enforceInvariant();
    }

    /**
     * @param array{rue: string, ville: string, code_postal: string, pays: string, complement?: string|null} $data
     */
    public static function fromArray(array $data): self
    {
        // Validation des champs requis
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException("Le champ '$field' est requis");
            }
        }

        return new self(
            rue: trim((string) $data[self::KEY_RUE]),
            ville: trim((string) $data[self::KEY_VILLE]),
            codePostal: trim((string) $data[self::KEY_CODE_POSTAL]),
            pays: trim((string) $data[self::KEY_PAYS]),
            complement: isset($data[self::KEY_COMPLEMENT]) ? trim((string) $data[self::KEY_COMPLEMENT]) : null,
        );
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?self
    {
        if ($data === null || empty($data)) {
            return null;
        }

        try {
            return self::fromArray($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function creer(
        string $rue,
        string $ville,
        string $codePostal,
        string $pays,
        ?string $complement = null
    ): self {
        return new self($rue, $ville, $codePostal, $pays, $complement);
    }

    /**
     * Retourne la liste des champs requis pour validation
     *
     * @return array<string>
     */
    public static function getRequiredFields(): array
    {
        return self::REQUIRED_FIELDS;
    }

    public function getAdresseComplete(): string
    {
        return implode("\n", $this->getAdresseParts());
    }

    public function getAdresseUneLigne(): string
    {
        return implode(', ', $this->getAdresseParts());
    }

    public function equals(Address $other): bool
    {
        return $this->rue === $other->rue
            && $this->ville === $other->ville
            && $this->codePostal === $other->codePostal
            && $this->pays === $other->pays
            && $this->complement === $other->complement;
    }

    /**
     * @return array{rue: string, ville: string, code_postal: string, pays: string, complement: string|null}
     */
    public function toArray(): array
    {
        return [
            self::KEY_RUE         => $this->rue,
            self::KEY_VILLE       => $this->ville,
            self::KEY_CODE_POSTAL => $this->codePostal,
            self::KEY_PAYS        => $this->pays,
            self::KEY_COMPLEMENT  => $this->complement,
        ];
    }

    /**
     * @return array<string>
     */
    private function getAdresseParts(): array
    {
        $parts = [
            $this->rue,
            $this->complement,
            "{$this->codePostal} {$this->ville}",
            $this->pays,
        ];

        return array_filter($parts, fn ($part) => !empty($part));
    }

    private function enforceInvariant(): void
    {
        $this->validateRue();
        $this->validateVille();
        $this->validateCodePostal();
        $this->validatePays();
        $this->validateComplement();
    }

    private function validateRue(): void
    {
        if (!v::notEmpty()->length(1, self::MAX_RUE_LENGTH)
            ->validate($this->rue)) {
            throw new InvalidArgumentException('La rue ne peut pas être vide et ne peut pas dépasser ' . self::MAX_RUE_LENGTH . ' caractères');
        }
    }

    private function validateVille(): void
    {
        if (!v::notEmpty()->length(1, self::MAX_VILLE_LENGTH)
            ->validate($this->ville)) {
            throw new InvalidArgumentException('La ville ne peut pas être vide et ne peut pas dépasser ' . self::MAX_VILLE_LENGTH . ' caractères');
        }
    }

    private function validateCodePostal(): void
    {
        $validation = Validator::make(
            ['code_postal' => $this->codePostal],
            ['code_postal' => "postal_code:{$this->pays}"]
        );

        if ($validation->fails()) {
            throw new InvalidArgumentException("Le code postal '{$this->codePostal}' n'est pas valide pour le pays '{$this->pays}'");
        }
    }

    private function validatePays(): void
    {
        if (!v::notEmpty()->length(1, self::MAX_PAYS_LENGTH)
            ->validate($this->pays)) {
            throw new InvalidArgumentException('Le pays ne peut pas être vide et ne peut pas dépasser ' . self::MAX_PAYS_LENGTH . ' caractères');
        }
    }

    private function validateComplement(): void
    {
        if ($this->complement !== null && !v::length(null, self::MAX_COMPLEMENT_LENGTH)->validate($this->complement)) {
            throw new InvalidArgumentException('Le complément d\'adresse ne peut pas dépasser ' . self::MAX_COMPLEMENT_LENGTH . ' caractères');
        }
    }
}
