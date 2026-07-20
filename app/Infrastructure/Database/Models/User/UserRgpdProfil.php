<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\User\UserRgpdProfilSchema;

/**
 * @property int $id
 * @property int $user_db_id
 * @property string $user_uuid
 * @property string $origin_type
 * @property \Illuminate\Support\Carbon $date_suppression
 * @property string|null $raison_suppression
 * @property array $donnees_conservees
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 */
final class UserRgpdProfil extends Model
{
    /** @use HasFactory<\Database\Factories\User\UserRgpdProfilFactory> */
    use HasFactory;

    protected $connection = UserRgpdProfilSchema::CONNECTION;

    protected $table = UserRgpdProfilSchema::FULL_TABLE;

    protected $primaryKey = UserRgpdProfilSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        UserRgpdProfilSchema::USER_KEY,
        UserRgpdProfilSchema::USER_ID,
        UserRgpdProfilSchema::ORIGIN_TYPE,
        UserRgpdProfilSchema::DATE_SUPPRESSION,
        UserRgpdProfilSchema::RAISON_SUPPRESSION,
        UserRgpdProfilSchema::DONNEES_CONSERVEES,
    ];

    /**
     * Get the user that owns this RGPD profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, UserRgpdProfilSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\User\UserRgpdProfilFactory
    {
        return \Database\Factories\User\UserRgpdProfilFactory::new();
    }

    protected function casts(): array
    {
        return [
            UserRgpdProfilSchema::DATE_SUPPRESSION   => 'datetime',
            UserRgpdProfilSchema::DONNEES_CONSERVEES => 'array',
            UserRgpdProfilSchema::CREATED_AT         => 'timestamp',
            UserRgpdProfilSchema::UPDATED_AT         => 'timestamp',
        ];
    }
}
