<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Auth;

use App\Enums\TokenType;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\Casts\AsIdentity;
use Laravel\Sanctum\Contracts\HasAbilities;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Auth\UserAccessTokenSchema;

/**
 * @property int $id
 * @property UserId $user_uuid
 * @property UserId $userUuid
 * @property TokenType $token_type
 * @property TokenType $tokenType
 * @property string $token
 * @property string $name
 * @property array<string> $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $lastUsedAt
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $expiresAt
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $updatedAt
 * @property-read User $user
 */
final class UserAccessToken extends Model implements HasAbilities
{
    /** @use HasFactory<\Database\Factories\Auth\UserAccessTokenFactory> */
    use HasFactory;

    protected $connection = UserAccessTokenSchema::CONNECTION;

    protected $table = UserAccessTokenSchema::FULL_TABLE;

    protected $fillable = [
        UserAccessTokenSchema::USER_ID,
        UserAccessTokenSchema::TOKEN_TYPE,
        UserAccessTokenSchema::TOKEN,
        UserAccessTokenSchema::NAME,
        UserAccessTokenSchema::LAST_USED_AT,
        UserAccessTokenSchema::EXPIRES_AT,
    ];

    protected $hidden = [
        UserAccessTokenSchema::TOKEN,
    ];

    protected $casts = [
        UserAccessTokenSchema::USER_ID      => AsIdentity::class . ':' . UserId::class,
        UserAccessTokenSchema::TOKEN_TYPE   => TokenType::class,
        UserAccessTokenSchema::LAST_USED_AT => 'timestamp',
        UserAccessTokenSchema::EXPIRES_AT   => 'timestamp',
        UserAccessTokenSchema::CREATED_AT   => 'timestamp',
        UserAccessTokenSchema::UPDATED_AT   => 'timestamp',
    ];

    /**
     * Find the token instance matching the given token.
     * Required by Sanctum Guard
     */
    public static function findToken(string $token): ?self
    {
        if (strpos($token, '|') === false) {
            return self::where(UserAccessTokenSchema::TOKEN, hash('sha256', $token))->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = self::find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }

        return null;
    }

    /**
     * Get the user that owns the access token.
     *
     * @return BelongsTo<User, UserAccessToken>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, UserAccessToken> */
        return $this->belongsTo(User::class, UserAccessTokenSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Alias for user() - required by Sanctum Guard
     *
     * @return BelongsTo<User, UserAccessToken>
     */
    public function tokenable(): BelongsTo
    {
        return $this->user();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update([UserAccessTokenSchema::LAST_USED_AT => now()]);
    }

    /**
     * Determine if the token has a given ability.
     * Always returns true - we use RBAC/ACL for permissions instead.
     */
    public function can($ability): bool
    {
        return true; // Token abilities disabled - use RBAC/ACL
    }

    /**
     * Determine if the token is missing a given ability.
     */
    public function cant($ability): bool
    {
        return !$this->can($ability);
    }

    /**
     * Get abilities for Sanctum compatibility (always empty - we use RBAC)
     *
     * @return array<string>
     */
    public function getAbilitiesAttribute(): array
    {
        return [];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Auth\UserAccessTokenFactory
    {
        return \Database\Factories\Auth\UserAccessTokenFactory::new();
    }
}
