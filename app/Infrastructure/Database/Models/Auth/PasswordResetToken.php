<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Auth\PasswordResetTokenSchema;

/**
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $createdAt
 */
final class PasswordResetToken extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $connection = PasswordResetTokenSchema::CONNECTION;

    protected $table = PasswordResetTokenSchema::FULL_TABLE;

    protected $primaryKey = PasswordResetTokenSchema::EMAIL;

    protected $keyType = 'string';

    protected $fillable = [
        PasswordResetTokenSchema::EMAIL,
        PasswordResetTokenSchema::TOKEN,
        PasswordResetTokenSchema::CREATED_AT,
    ];

    protected $casts = [
        PasswordResetTokenSchema::CREATED_AT => 'timestamp',
    ];

    public static function createForEmail(string $email, string $token): self
    {
        return self::create([
            PasswordResetTokenSchema::EMAIL      => $email,
            PasswordResetTokenSchema::TOKEN      => $token,
            PasswordResetTokenSchema::CREATED_AT => now(),
        ]);
    }

    public static function deleteForEmail(string $email): bool
    {
        return self::where(PasswordResetTokenSchema::EMAIL, $email)->delete() > 0;
    }

    public function isExpired(int $expireMinutes = 60): bool
    {
        if (!$this->created_at) {
            return true;
        }

        return $this->created_at->addMinutes($expireMinutes)->isPast();
    }
}
