<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\Casts\AsIdentity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;

/**
 * @property int $id
 * @property UserId $user_uuid
 * @property UserId $userUuid
 * @property string $email
 * @property string $password_hash
 * @property string $passwordHash
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $emailVerifiedAt
 * @property string|null $remember_token
 * @property string|null $rememberToken
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $updatedAt
 * @property-read User $user
 */
final class UserCredential extends Model
{
    /** @use HasFactory<\Database\Factories\Auth\UserCredentialFactory> */
    use HasFactory;

    // Relations constants
    public const RELATION_USER = 'user';

    protected $connection = UserCredentialSchema::CONNECTION;

    protected $table = UserCredentialSchema::FULL_TABLE;

    protected $fillable = [
        UserCredentialSchema::USER_ID,
        UserCredentialSchema::USER_KEY,
        UserCredentialSchema::EMAIL,
        UserCredentialSchema::PASSWORD_HASH,
        UserCredentialSchema::EMAIL_VERIFIED_AT,
        UserCredentialSchema::REMEMBER_TOKEN,
    ];

    protected $hidden = [
        UserCredentialSchema::PASSWORD_HASH,
        UserCredentialSchema::REMEMBER_TOKEN,
    ];

    protected $casts = [
        UserCredentialSchema::USER_ID           => AsIdentity::class . ':' . UserId::class,
        UserCredentialSchema::EMAIL_VERIFIED_AT => 'timestamp',
        UserCredentialSchema::CREATED_AT        => 'timestamp',
        UserCredentialSchema::UPDATED_AT        => 'timestamp',
    ];

    /**
     * Get the user that owns the credential.
     *
     * @return BelongsTo<User, UserCredential>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, UserCredential> */
        return $this->belongsTo(User::class, UserCredentialSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->update([UserCredentialSchema::EMAIL_VERIFIED_AT => now()]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Auth\UserCredentialFactory
    {
        return \Database\Factories\Auth\UserCredentialFactory::new();
    }
}
