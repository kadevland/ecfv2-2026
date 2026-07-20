<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\User\UserRgpdConsentementSchema;

/**
 * @property int $id
 * @property int $user_db_id
 * @property string $user_uuid
 * @property string $type_consentement
 * @property bool $consentement_donne
 * @property \Illuminate\Support\Carbon|null $date_consentement
 * @property \Illuminate\Support\Carbon|null $date_retrait
 * @property string|null $ip_consentement
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 */
final class UserRgpdConsentement extends Model
{
    use HasFactory;

    // @phpstan-ignore class.notFound
    protected $connection = UserRgpdConsentementSchema::CONNECTION;

    // @phpstan-ignore class.notFound
    protected $table = UserRgpdConsentementSchema::FULL_TABLE;

    // @phpstan-ignore class.notFound
    protected $primaryKey = UserRgpdConsentementSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::USER_KEY,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::USER_ID,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::TYPE_CONSENTEMENT,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::CONSENTEMENT_DONNE,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::DATE_CONSENTEMENT,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::DATE_RETRAIT,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::IP_CONSENTEMENT,
        // @phpstan-ignore class.notFound
        UserRgpdConsentementSchema::USER_AGENT,
    ];

    /**
     * Get the user that owns this consent.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        // @phpstan-ignore class.notFound
        return $this->belongsTo(User::class, UserRgpdConsentementSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Check if consent is currently active
     */
    public function isActive(): bool
    {
        return $this->consentement_donne && $this->date_retrait === null;
    }

    /**
     * Retract consent
     */
    public function retract(): self
    {
        $this->update([
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::CONSENTEMENT_DONNE => false,
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::DATE_RETRAIT => now(),
        ]);

        return $this;
    }

    /**
     * Create a new factory instance for the model.
     */
    /**
     * @phpstan-ignore-next-line class.notFound
     */
    protected static function newFactory(): \Database\Factories\User\UserRgpdConsentementFactory
    {
        // @phpstan-ignore class.notFound
        return \Database\Factories\User\UserRgpdConsentementFactory::new();
    }

    protected function casts(): array
    {
        return [
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::CONSENTEMENT_DONNE => 'boolean',
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::DATE_CONSENTEMENT => 'datetime',
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::DATE_RETRAIT => 'datetime',
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::CREATED_AT => 'timestamp',
            // @phpstan-ignore class.notFound
            UserRgpdConsentementSchema::UPDATED_AT => 'timestamp',
        ];
    }
}
