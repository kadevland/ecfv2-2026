<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Auth;

use DateTimeInterface;
use App\Enums\UserType;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\Casts\AsIdentity;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Concerns\HasUuidFinder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Models\Profiles\EmployeeProfile;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Infrastructure\Database\Schemas\Auth\UserAccessTokenSchema;
use App\Infrastructure\Database\Schemas\Profiles\EmployeeProfileSchema;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

/**
 * @property int $db_id - PK auto-increment pour performances (technique)
 * @property UserId $id - Identifiant business/domain (DDD)
 * @property UserType $type
 * @property bool $is_active
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $updatedAt
 * @property-read UserCredential|null $credential
 * @property-read ClientProfile|null $clientProfile
 * @property-read EmployeeProfile|null $employeeProfile
 * @property-read UserProfil|null $profil
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserAccessToken> $accessTokens
 */
final class User extends Authenticatable implements CanResetPassword
{
    /** @use HasFactory<\Database\Factories\Auth\UserFactory> */
    use CanResetPasswordTrait, HasApiTokens, HasFactory, HasUuidFinder, HasUuids;

    // Relations constants
    public const RELATION_CREDENTIAL = 'credential';

    public const RELATION_CLIENT_PROFILE = 'clientProfile';

    public const RELATION_EMPLOYEE_PROFILE = 'employeeProfile';

    public const RELATION_ACCESS_TOKENS = 'accessTokens';

    public const RELATION_PROFIL = 'profil';

    protected $connection = UserSchema::CONNECTION;

    protected $table = UserSchema::FULL_TABLE;

    protected $primaryKey = UserSchema::PRIMARY_KEY; // db_id (auto-increment)

    protected $fillable = [
        UserSchema::ID,          // Domain peut assigner l'ID
        UserSchema::TYPE,
        UserSchema::IS_ACTIVE,
    ];

    /**
     * The access token the user is using for the current request.
     */
    protected ?UserAccessToken $currentUserAccessToken = null;

    /**
     * Summary of credential
     *
     * @return HasOne<UserCredential, User>
     */
    public function credential(): HasOne
    {
        /** @var HasOne<UserCredential, User> */
        return $this->hasOne(UserCredential::class, UserCredentialSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Summary of clientProfile
     *
     * @return HasOne<UserProfil, User>
     */
    public function clientProfile(): HasOne
    {
        /** @var HasOne<UserProfil, User> */
        return $this->hasOne(UserProfil::class, UserProfilSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    public function userProfile(): HasOne
    {
        /** @var HasOne<UserProfil, User> */
        return $this->hasOne(UserProfil::class, UserProfilSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Summary of employeeProfile
     *
     * @return HasOne<EmployeeProfile, User>
     */
    public function employeeProfile(): HasOne
    {
        /** @var HasOne<EmployeeProfile, User> */
        return $this->hasOne(EmployeeProfile::class, EmployeeProfileSchema::USER_DB_ID, UserSchema::PRIMARY_KEY);
    }

    /**
     * Summary of accessTokens
     *
     * @return HasMany<UserAccessToken, User>
     */
    public function accessTokens(): HasMany
    {
        /** @var HasMany<UserAccessToken, User> */
        return $this->hasMany(UserAccessToken::class, UserAccessTokenSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Get the user profile (unified profile model)
     *
     * @return HasOne<UserProfil, User>
     */
    public function profil(): HasOne
    {
        /** @var HasOne<UserProfil, User> */
        return $this->hasOne(UserProfil::class, UserProfilSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    public function getAuthPassword(): ?string
    {
        return $this->credential?->password_hash;
    }

    public function getAuthIdentifierName(): string
    {
        return UserSchema::ID;
    }

    public function getEmailForPasswordReset(): ?string
    {
        return $this->credential?->email;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     * Override from CanResetPassword trait
     */
    public function getEmailForVerification(): string
    {
        return $this->getEmailForPasswordReset() ?? '';
    }

    public function isClient(): bool
    {
        return $this->type === UserType::CLIENT;
    }

    public function isEmployee(): bool
    {
        return $this->type === UserType::EMPLOYEE;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN;
    }

    public function isDeleted(): bool
    {
        return $this->type === UserType::CLIENT_DELETED;
    }

    /**
     * Get the remember token for the user.
     */
    public function getRememberToken(): ?string
    {
        // Load credential if not already loaded
        if (!$this->relationLoaded(self::RELATION_CREDENTIAL)) {
            $this->load(self::RELATION_CREDENTIAL);
        }

        return $this->credential?->remember_token;
    }

    /**
     * Set the remember token for the user.
     */
    public function setRememberToken($value): void
    {
        // Load credential if not already loaded
        if (!$this->relationLoaded(self::RELATION_CREDENTIAL)) {
            $this->load(self::RELATION_CREDENTIAL);
        }

        if ($this->credential) {
            $this->credential->remember_token = $value;
            $this->credential->save();
        }
    }

    /**
     * Get the remember token column name.
     */
    public function getRememberTokenName(): string
    {
        return UserCredentialSchema::REMEMBER_TOKEN;
    }

    /**
     * Create a new access token for the user.
     *
     * @param array<string> $abilities
     */
    public function createToken(string $name, array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): object
    {
        $plainTextToken = $this->generateTokenString();

        $token = $this->accessTokens()
            ->create([
                UserAccessTokenSchema::TOKEN_TYPE => \App\Enums\TokenType::API,
                UserAccessTokenSchema::TOKEN      => hash('sha256', $plainTextToken),
                UserAccessTokenSchema::NAME       => $name,
                UserAccessTokenSchema::EXPIRES_AT => $expiresAt,
            ]);

        return new class($token, $plainTextToken)
        {
            public function __construct(
                public readonly UserAccessToken $accessToken,
                public readonly string $plainTextToken
            ) {}
        };
    }

    /**
     * Get the access token currently associated with the user.
     */
    public function currentAccessToken(): ?UserAccessToken
    {
        return $this->currentUserAccessToken;
    }

    /**
     * Set the current access token for the user.
     */
    public function withAccessToken(UserAccessToken $accessToken): self
    {
        $this->currentUserAccessToken = $accessToken;

        return $this;
    }

    /**
     * Determine if the current API token has a given ability.
     */
    public function tokenCan(string $ability): bool
    {
        return $this->currentUserAccessToken && $this->currentUserAccessToken->can($ability);
    }

    /**
     * Determine if the current API token is missing a given ability.
     */
    public function tokenCant(string $ability): bool
    {
        return !$this->tokenCan($ability);
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return string[]
     */
    public function uniqueIds(): array
    {
        return [UserSchema::ID];
    }

    /**
     * Get the auto-incrementing primary key type.
     */
    public function getKeyType(): string
    {
        return 'int';
    }

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public function getIncrementing(): bool
    {
        return true;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Auth\UserFactory
    {
        return \Database\Factories\Auth\UserFactory::new();
    }

    /**
     * Nom de la colonne UUID - méthode statique optimisée
     */
    protected static function getUuidColumnName(): string
    {
        return UserSchema::ID;
    }

    protected function casts(): array
    {
        return [
            UserSchema::ID         => AsIdentity::class . ':' . UserId::class, // uuid -> UserId
            UserSchema::TYPE       => UserType::class,
            UserSchema::IS_ACTIVE  => 'boolean',
            UserSchema::CREATED_AT => 'datetime',
            UserSchema::UPDATED_AT => 'datetime',
        ];
    }

    /**
     * Generate a new token string.
     */
    protected function generateTokenString(): string
    {
        return sprintf(
            '%s%s%s',
            config('sanctum.token_prefix', ''),
            $tokenEntropy = \Illuminate\Support\Str::random(40),
            hash('crc32b', $tokenEntropy)
        );
    }

    /**
     * Classe d'identité pour ce modèle
     */
    protected function getUuidIdentityClass(): string
    {
        return UserId::class;
    }
}
