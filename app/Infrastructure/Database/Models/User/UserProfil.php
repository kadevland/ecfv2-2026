<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\User;

use App\Infrastructure\Casts\AsNom;
use App\Domain\Shared\Enums\SexeEnum;
use App\Infrastructure\Casts\AsPrenom;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsPhoneNumber;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

/**
 * @property int $id
 * @property int $user_db_id
 * @property string $user_uuid
 * @property \App\Domain\Shared\ValueObjects\Prenom $prenom
 * @property \App\Domain\Shared\ValueObjects\Nom $nom
 * @property \Illuminate\Support\Carbon|null $date_naissance
 * @property \App\Domain\Shared\ValueObjects\PhoneNumber|null $telephone
 * @property array|null $adresse
 * @property array|null $preferences
 * @property bool $newsletter
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 */
final class UserProfil extends Model
{
    /** @use HasFactory<\Database\Factories\User\UserProfilFactory> */
    use HasFactory;

    protected $connection = UserProfilSchema::CONNECTION;

    protected $table = UserProfilSchema::FULL_TABLE;

    protected $primaryKey = UserProfilSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        UserProfilSchema::USER_KEY,
        UserProfilSchema::USER_ID,
        UserProfilSchema::PRENOM,
        UserProfilSchema::NOM,
        UserProfilSchema::DATE_NAISSANCE,
        UserProfilSchema::TELEPHONE,
        UserProfilSchema::SEXE,
        UserProfilSchema::ADRESSE,
        UserProfilSchema::PREFERENCES,
        UserProfilSchema::NEWSLETTER,
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
        return trim($this->prenom->toString() . ' ' . $this->nom->toString());
    }

    /**
     * Get age from date_naissance.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->diffInYears(now());
    }

    /**
     * Get street address from JSONB adresse field
     */
    public function getRueAttribute(): ?string
    {
        return $this->adresse[UserProfilSchema::ADRESSE_RUE] ?? null;
    }

    /**
     * Get city from JSONB adresse field
     */
    public function getVilleAttribute(): ?string
    {
        return $this->adresse[UserProfilSchema::ADRESSE_VILLE] ?? null;
    }

    /**
     * Get postal code from JSONB adresse field
     */
    public function getCodePostalAttribute(): ?string
    {
        return $this->adresse[UserProfilSchema::ADRESSE_CODE_POSTAL] ?? null;
    }

    /**
     * Get country from JSONB adresse field
     */
    public function getPaysAttribute(): ?string
    {
        return $this->adresse[UserProfilSchema::ADRESSE_PAYS] ?? null;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\User\UserProfilFactory
    {
        return \Database\Factories\User\UserProfilFactory::new();
    }

    /**
     * Get the casts array.
     */
    protected function casts(): array
    {
        return [
            UserProfilSchema::DATE_NAISSANCE => 'date',
            UserProfilSchema::PRENOM         => AsPrenom::class,
            UserProfilSchema::NOM            => AsNom::class,
            UserProfilSchema::TELEPHONE      => AsPhoneNumber::class,
            UserProfilSchema::SEXE           => SexeEnum::class,
            UserProfilSchema::ADRESSE        => 'array',
            UserProfilSchema::PREFERENCES    => 'array',
            UserProfilSchema::NEWSLETTER     => 'boolean',
        ];
    }
}
