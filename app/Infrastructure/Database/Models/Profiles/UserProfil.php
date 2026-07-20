<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Profiles;

use App\Enums\UserType;
use App\Domain\Shared\Enums\SexeEnum;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

/**
 * @property int $id
 * @property int $user_db_id
 * @property string $user_uuid
 * @property UserType $type
 * @property string $prenom
 * @property string $nom
 * @property string $email
 * @property string|null $telephone
 * @property \Illuminate\Support\Carbon|null $date_naissance
 * @property SexeEnum|null $sexe
 * @property array|null $adresse
 * @property array|null $preferences
 * @property bool $newsletter
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 */
final class UserProfil extends Model
{
    /** @use HasFactory<\Database\Factories\Profiles\UserProfilFactory> */
    use HasFactory;

    protected $connection = UserProfilSchema::CONNECTION;

    protected $table = UserProfilSchema::FULL_TABLE;

    protected $primaryKey = UserProfilSchema::PRIMARY_KEY;

    protected $fillable = [
        UserProfilSchema::ID,           // uuid
        UserProfilSchema::USER_KEY,
        UserProfilSchema::USER_ID,
        UserProfilSchema::TYPE,
        UserProfilSchema::PRENOM,
        UserProfilSchema::NOM,
        UserProfilSchema::EMAIL,
        UserProfilSchema::TELEPHONE,
        UserProfilSchema::DATE_NAISSANCE,
        UserProfilSchema::SEXE,
        UserProfilSchema::ADRESSE,
        UserProfilSchema::PREFERENCES,
        UserProfilSchema::NEWSLETTER,
    ];

    protected $casts = [
        UserProfilSchema::USER_ID        => 'string',
        UserProfilSchema::TYPE           => UserType::class,
        UserProfilSchema::SEXE           => SexeEnum::class,
        UserProfilSchema::DATE_NAISSANCE => 'date',
        UserProfilSchema::ADRESSE        => 'array',
        UserProfilSchema::PREFERENCES    => 'array',
        UserProfilSchema::NEWSLETTER     => 'boolean',
        UserProfilSchema::CREATED_AT     => 'datetime',
        UserProfilSchema::UPDATED_AT     => 'datetime',
    ];

    /**
     * Get the user that owns this profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, UserProfilSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Get full name (prenom + nom).
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    /**
     * Get age from date_naissance.
     */
    public function getAgeAttribute(): ?float
    {
        return $this->date_naissance?->diffInYears(now());
    }

    /**
     * Check if this is a client profile (using local type field for performance).
     */
    public function isClient(): bool
    {
        return $this->type === UserType::CLIENT;
    }

    /**
     * Check if this is an employee profile (using local type field for performance).
     */
    public function isEmployee(): bool
    {
        return in_array($this->type, [UserType::EMPLOYEE, UserType::ADMIN]);
    }

    /**
     * Check if this is an admin profile (using local type field for performance).
     */
    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Profiles\UserProfilFactory
    {
        return \Database\Factories\Profiles\UserProfilFactory::new();
    }
}
